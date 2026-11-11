<?php
require __DIR__ . '/wpml-builder-contract.php';
function delete_metadata($type, $object, $key, $value, $all) {
    expect($all && $type === 'post', 'Expected bounded native metadata cleanup.');
    foreach ($GLOBALS['meta'] as &$entry) { if (($entry[$key] ?? null) === $value) unset($entry[$key]); }
}
require dirname(__DIR__) . '/radmanct/inc/admin/builder-api.php';
use RadmanCT\Builder\Storage as S;
use RadmanCT\Admin\BuilderApi as A;
foreach (['header','footer'] as $kind) {
    $layout = ['version'=>1,'enabled'=>false,'rows'=>[]];
    expect(S\rename($kind, 'default', 'Renamed original'), 'Original rename failed.');
    $titles = array_column(S\listing($kind), 'title', 'id');
    expect($titles['default'] === 'Renamed original', 'Original title not reflected.');
    $other = S\create($kind, 'Independent', $layout);
    $snapshot = RadmanCT\Builder\read($kind, $other);
    set_theme_mod('radman_builder_defaults', [$kind=>['all'=>'default','fa'=>'default','en'=>$other]]);
    $GLOBALS['meta'][999]['_radman_' . $kind . '_layout'] = 'default';
    expect(A\delete_layout($kind, 'default') === null, 'Original deletion failed.');
    expect(!S\exists($kind, 'default') && !isset(array_column(S\listing($kind), 'title', 'id')['default']), 'Original resurrected.');
    expect(S\save($kind, 'default', $layout, 0)['status'] === 'conflict', 'Stale editor recreated deleted original.');
    expect(A\defaults()[$kind] === ['all'=>'none','fa'=>'none','en'=>$other], 'Language assignments not cleaned selectively.');
    expect(!isset($GLOBALS['meta'][999]['_radman_' . $kind . '_layout']), 'Page assignment not cleaned.');
    expect($snapshot === RadmanCT\Builder\read($kind, $other), 'Independent layout changed.');
    expect(is_array(A\state($kind, 'none')['data']), 'Hidden assignment response failed.');
    expect(is_string(S\create($kind, 'After deletion', $layout)), 'Cannot create after original deletion.');
}
echo "Passed: original rename/delete, tombstone, stale-save rejection, selective site/language/page cleanup, independent layout preservation and creation after deletion. Stub storage only.\n";
