<?php
$attrsString = '';
if (!empty($attrs) && is_array($attrs)) {
    foreach ($attrs as $k => $v) {
        $attrsString .= ' ' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') .
            '="' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . '"';
    }
}
?>
<button<?= $attrsString ?>><?= htmlspecialchars($label ?? 'Button', ENT_QUOTES, 'UTF-8') ?></button>
