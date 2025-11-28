<?php
$flashAreas = ['auth', 'contracts', 'users', 'profile'];
foreach ($flashAreas as $key) {
    if ($flash = getFlash($key)) {
        echo '<div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast show text-bg-' . $flash['type'] . '" role="alert">
                <div class="toast-body">' . $flash['message'] . '</div>
            </div>
        </div>';
    }
}
?>
