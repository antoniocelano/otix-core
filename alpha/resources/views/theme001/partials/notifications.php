<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Session.php';
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Notify.php';


$notification = \App\Core\Notify::getAndForget();

if ($notification):
    $message = htmlspecialchars($notification['message']);
    $duration = (int) $notification['duration'] * 1000;
    $type = htmlspecialchars($notification['type']);
    
    $classes = [
        'success' => 'bg-success text-white',
        'error' => 'bg-danger text-white',
        'warning' => 'bg-warning text-dark',
        'info' => 'bg-info text-white',
    ];
    $templateClass = $classes[$type] ?? 'bg-secondary text-white';
?>
<style>
    .notify-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
    }
    .notify-alert {
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }
    .notify-alert.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>
<div id="notify-div" class="notify-container">
    <div class="notify-alert <?= $templateClass ?>">
        <?= $message ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifyDiv = document.getElementById('notify-div');
        const notifyAlert = notifyDiv.querySelector('.notify-alert');
        
        setTimeout(() => {
            notifyAlert.classList.add('show');
        }, 100);

        setTimeout(() => {
            notifyAlert.classList.remove('show');
            setTimeout(() => {
                notifyDiv.remove();
            }, 500); 
        }, <?= $duration ?>);
    });
</script>
<?php endif; ?>