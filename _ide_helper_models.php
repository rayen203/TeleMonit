<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $dateCreation
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $screenshots
 * @property-read int|null $screenshots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teletravailleur> $teletravailleurs
 * @property-read int|null $teletravailleurs_count
 * @property-read \App\Models\Utilisateur|null $utilisateur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkingHour> $workingHours
 * @property-read int|null $working_hours_count
 * @method static \Database\Factories\AdministrateurFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur query()
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur whereDateCreation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Administrateur whereUserId($value)
 */
	class Administrateur extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date
 * @property array $tacheList
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utilisateur $utilisateur
 * @method static \Database\Factories\CalendarFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereTacheList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereUserId($value)
 */
	class Calendar extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $sessionId
 * @property array|null $historique
 * @property int $teletravailleur_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Teletravailleur $teletravailleur
 * @method static \Database\Factories\ChatbotFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereHistorique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereTeletravailleurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereUpdatedAt($value)
 */
	class Chatbot extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $contenu
 * @property int $teletravailleur_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Teletravailleur $teletravailleur
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereContenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereTeletravailleurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $image_path
 * @property int $teletravailleur_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Teletravailleur $teletravailleur
 * @method static \Database\Factories\ScreenshotFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot query()
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot whereTeletravailleurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Screenshot whereUpdatedAt($value)
 */
	class Screenshot extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $setting_name
 * @property string $setting_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SystemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|System newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|System newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|System query()
 * @method static \Illuminate\Database\Eloquent\Builder|System whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereSettingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereSettingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|System whereUpdatedAt($value)
 */
	class System extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $CIN
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $photoProfil
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $token
 * @property-read \App\Models\Chatbot|null $chatbots
 * @property-read mixed $total_heures
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $screenshots
 * @property-read int|null $screenshots_count
 * @property-read \App\Models\Utilisateur $utilisateur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkingHour> $workingHours
 * @property-read int|null $working_hours_count
 * @method static \Database\Factories\TeletravailleurFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereCIN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur wherePhotoProfil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teletravailleur whereUserId($value)
 */
	class Teletravailleur extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property bool $statut
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_activity
 * @property-read \App\Models\Administrateur|null $administrateur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Calendar> $calendars
 * @property-read int|null $calendars_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Teletravailleur|null $teletravailleur
 * @method static \Database\Factories\UtilisateurFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur query()
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Utilisateur whereUpdatedAt($value)
 */
	class Utilisateur extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $teletravailleur_id
 * @property \Illuminate\Support\Carbon|null $date
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $pause_time
 * @property \Illuminate\Support\Carbon|null $resume_time
 * @property \Illuminate\Support\Carbon|null $stop_time
 * @property int $total_seconds
 * @property int $pause_total_seconds
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_time
 * @property-read mixed $total_hours
 * @property-read \App\Models\Teletravailleur $teletravailleur
 * @method static \Database\Factories\WorkingHourFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour wherePauseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour wherePauseTotalSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereResumeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereStopTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereTeletravailleurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereTotalSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingHour whereUpdatedAt($value)
 */
	class WorkingHour extends \Eloquent {}
}

