$lines = Get-Content views/view.php; 
$filtered = @(); 
foreach ($line in $lines) 
{ 
    if ($line -match '^[ \t]*<?php$') { continue } 
    if ($line -match '^[ \t]*\$user_id') { continue } 
    if ($line -match '^[ \t]*\?>$') { continue } 
    $filtered += $line 
} 
$filtered | Set-Content views/view.php 
