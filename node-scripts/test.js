import screenshot from 'screenshot-desktop';
import { writeFile } from 'fs';
import { join } from 'path';
import { fileURLToPath } from 'url';

// Convertir __dirname en mode ES Modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = join(__filename, '..');

// Modifier le chemin pour enregistrer l’image dans "public/"
const outputPath = join(__dirname, '../public/test-capture.png');

// Prendre une capture d’écran
screenshot({ filename: outputPath })
    .then((imgPath) => {
        console.log(`✅ Capture réussie ! Image enregistrée sous ${imgPath}`);
    })
    .catch((err) => {
        console.error('❌ Erreur lors de la capture d’écran :', err);
    });
