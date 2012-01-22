<?php
$make_sure_all_items_have_key = function(&$items, $k) {
    foreach($items as $item_key => $item) {
        if(!isset($item[$k])) {
            $items[$item_key][$k] = "";
        }
    }
};

$options_array = function($key, $priority) use ($model) {
    return isset($model["{$priority}_{$key}_attributes"])
        ? $model["{$priority}_{$key}_attributes"]
        : (isset($model["{$key}_attributes"])
            ? $model["{$key}_attributes"]
            : array());
};

$labels = isset($model['labels'])
    ? $model['labels']
    : array();

$labels_html = "";

$label_column_attributes = $options_array('column', 'label');

foreach($labels as $k => $v) {
    $labels_html .= $h->th($label_column_attributes, $v) . "\n";

    $make_sure_all_items_have_key($model['list'], $k);
}

foreach($model['list'] as $_ => $item) {
    foreach($item as $item_key => $_) {
        $make_sure_all_items_have_key($model['list'], $item_key);

        if(!isset($labels[$item_key])) {
            $labels[$item_key] = $item_key;
            $labels_html .= $h->th($label_column_attributes, $item_key) . "\n";
        }
    }
}

$items_html = "";

foreach($model['list'] as $_ => $item) {
    $row_body = "";

    foreach($labels as $label_key => $_) {
        $row_body .= $h->td($options_array('column', 'item'), $item[$label_key]) . "\n";
    }

    $items_html .= $h->tr($options_array('row', 'item'), $row_body);
}
?>

<?= $h->table(isset($model['table_attributes']) ? $model['table_attributes'] : array(), $h->concat(
    $labels_html == '' ? '' : $h->tr($options_array('row', 'label'), $labels_html),
    $items_html
)) ?>