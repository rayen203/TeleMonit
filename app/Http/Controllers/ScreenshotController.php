<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScreenshotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Enregistre une capture d’écran.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $teletravailleur = Auth::user()->teletravailleur;

        if (!$teletravailleur) {
            Log::error('Télétravailleur non trouvé lors de la capture d’écran.', ['user_id' => Auth::id()]);
            return response()->json(['error' => 'Télétravailleur non trouvé.'], 404);
        }

        // Validation du format base64
        $request->validate([
            'screenshot' => [
                'required',
                'string',
                'regex:/^data:image\/png;base64,/', // Assouplir la regex pour permettre plus de caractères
                function ($attribute, $value, $fail) {
                    // Nettoyer la chaîne avant validation
                    $base64String = str_replace('data:image/png;base64,', '', $value);
                    $base64String = str_replace(' ', '+', $base64String);
                    $base64String = preg_replace('/\s+/', '', $base64String); // Supprimer les retours à la ligne et espaces

                    // Vérifier la taille approximative
                    $sizeInBytes = (strlen($base64String) * 3) / 4 - substr_count($base64String, '=');
                    if ($sizeInBytes > 5 * 1024 * 1024) {
                        $fail('L’image est trop volumineuse (max: 5 Mo).');
                    }
                    if ($sizeInBytes < 100) {
                        $fail('L’image est trop petite (min: 100 octets).');
                    }

                    // Vérifier si la chaîne est un base64 valide
                    $decoded = base64_decode($base64String, true);
                    if ($decoded === false) {
                        $fail('Format base64 invalide.');
                    }
                },
            ],
        ]);

        try {
            // Extraction et décodage de l'image base64
            $imageData = str_replace('data:image/png;base64,', '', $request->input('screenshot'));
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = preg_replace('/\s+/', '', $imageData); // Supprimer les retours à la ligne et espaces
            $image = base64_decode($imageData, true);

            if ($image === false) {
                Log::error('Échec du décodage de l’image base64.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'base64_length' => strlen($imageData),
                    'base64_sample' => substr($imageData, 0, 100), // Ajouter un échantillon pour débogage
                ]);
                return response()->json(['error' => 'Format de l’image invalide.'], 422);
            }

            $imageSize = strlen($image);
            Log::info('Taille de l’image après décodage', ['size' => $imageSize]);
            if ($imageSize < 100 || $imageSize > 5 * 1024 * 1024) {
                Log::error('Taille de l’image invalide après décodage.', [
                    'teletravailleur_id' => $teletravailleur->id,
                    'size' => $imageSize
                ]);
                return response()->json(['error' => 'Taille de l’image invalide.'], 422);
            }

            // Supprimer la vérification stricte du type PNG pour l'instant
            // $imageResource = imagecreatefromstring($image);
            // if ($imageResource === false) {
            //     Log::error('L’image n’est pas une image PNG valide.', [
            //         'teletravailleur_id' => $teletravailleur->id,
            //     ]);
            //     return response()->json(['error' => 'L’image n’est pas une image PNG valide.'], 422);
            // }
            // imagedestroy($imageResource);

            $filename = 'screenshots/' . $teletravailleur->id . '_' . now()->format('Ymd_His') . '.png';

            Storage::disk('public')->put($filename, $image);
            Log::info('Image sauvegardée dans le stockage.', [
                'filename' => $filename,
                'size' => $imageSize
            ]);

            if (!Storage::disk('public')->exists($filename)) {
                Log::error('Échec de la sauvegarde de l’image.', [
                    'filename' => $filename,
                    'teletravailleur_id' => $teletravailleur->id
                ]);
                return response()->json(['error' => 'Échec de la sauvegarde de l’image.'], 500);
            }

            $screenshot = Screenshot::create([
                'teletravailleur_id' => $teletravailleur->id,
                'image_path' => $filename,
            ]);

            Log::info('Capture d’écran enregistrée avec succès.', [
                'teletravailleur_id' => $teletravailleur->id,
                'screenshot_id' => $screenshot->id,
            ]);

            return response()->json([
                'message' => 'Capture d’écran enregistrée avec succès.',
                'image_url' => asset('storage/' . $filename),
                'screenshot_id' => $screenshot->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l’enregistrement de la capture d’écran.', [
                'teletravailleur_id' => $teletravailleur->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Erreur serveur lors de l’enregistrement.'], 500);
        }
    }

    /**
     * Capture une capture d’écran via un script Node.js.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function capture(Request $request)
    {
        Log::info('Début de la capture d’écran via Node.js', ['user_id' => Auth::id()]);

        // Chemin vers le script Node.js
        $command = escapeshellcmd('node ' . base_path('node-scripts/test.js'));

        // Exécuter la commande et capturer la sortie
        $output = [];
        $resultCode = null;
        exec($command . ' 2>&1', $output, $resultCode);

        // Vérifier si la capture a réussi
        if ($resultCode === 0) {
            // Attendre 1 seconde pour s'assurer que l'image est bien écrite
            sleep(1);

            // Vérifier que l'image a été créée
            $imagePath = public_path('test-capture.png');
            if (file_exists($imagePath)) {
                // Vérifier la taille de l'image
                $imageSize = filesize($imagePath);
                Log::info('Taille de test-capture.png après exécution de test.js', ['size' => $imageSize]);
                if ($imageSize < 100) {
                    Log::error('L’image capturée est trop petite ou corrompue.', [
                        'image_path' => $imagePath,
                        'size' => $imageSize
                    ]);
                    return response()->json([
                        'message' => 'Erreur : l’image capturée est corrompue ou trop petite.',
                        'size' => $imageSize
                    ], 500);
                }

                // Lire l'image et la convertir en base64
                $imageData = file_get_contents($imagePath);
                if ($imageData === false) {
                    Log::error('Échec de la lecture de l’image capturée.', [
                        'image_path' => $imagePath
                    ]);
                    return response()->json([
                        'message' => 'Erreur : échec de la lecture de l’image capturée.'
                    ], 500);
                }

                $base64Image = 'data:image/png;base64,' . base64_encode($imageData);
                Log::info('Image convertie en base64 avec succès', ['base64_length' => strlen($base64Image)]);

                // Simuler une requête pour la méthode store
                $request->merge(['screenshot' => $base64Image]);
                return $this->store($request);
            } else {
                Log::error('Fichier de capture non trouvé après exécution du script.', [
                    'command' => $command,
                    'output' => $output
                ]);
                return response()->json([
                    'message' => 'Erreur : fichier de capture non trouvé.'
                ], 500);
            }
        } else {
            Log::error('Erreur lors de la capture d\'écran via Node.js.', [
                'command' => $command,
                'output' => implode("\n", $output),
                'result_code' => $resultCode
            ]);
            return response()->json([
                'message' => 'Erreur de capture d\'écran.',
                'details' => implode("\n", $output)
            ], 500);
        }
    }
}
