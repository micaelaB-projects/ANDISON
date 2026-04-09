<?php
require __DIR__ . '/Andison/includes/brands_info.php';
$all = andison_get_brands_info(true);

function disp($b){
  $n=strtolower(trim($b));
  if($n==='panasonic'||$n==='panasonic connect') return 'Panasonic Connect';
  if($n==='rae'||$n==='rac'||$n==='rae systems') return 'RAE SYSTEMS';
  if($n==='weller'||$n==='weiler') return 'WEILER';
  if($n==='robot systems peripherals'||$n==='robot systems'||$n==='robot system peripherals') return 'Robot Systems';
  return $b;
}

$map=[];
foreach(array_keys($all) as $k){
  $dk=strtolower(trim(disp($k)));
  if($dk==='') continue;
  if(!isset($map[$dk])) { $map[$dk]=$k; continue; }
  $cur=count($all[$map[$dk]]['products'] ?? []);
  $new=count($all[$k]['products'] ?? []);
  if($new>$cur) $map[$dk]=$k;
}

$brand='Robot Systems';
$dk=strtolower(trim(disp($brand)));
$key=$map[$dk] ?? '';
echo 'ResolvedKey=' . $key . PHP_EOL;
$rows=$key!=='' ? ($all[$key]['products'] ?? []) : [];
echo 'Count=' . count($rows) . PHP_EOL;
foreach($rows as $i=>$p){
  $m=(string)($p['model'] ?? '');
  $name=(string)($p['product_name'] ?? '');
  $badge=(string)($p['badge'] ?? '');
  echo ($i+1) . '. ' . $m . ' | ' . $name . ' | badge=[' . $badge . ']' . PHP_EOL;
}
?>
