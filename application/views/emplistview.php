////Scratch

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div>
<input></intput><button>Search</button>
</div>
<div>
	<table>
		<?=
			foreach($value in $list){
				echo "<tr>";
				echo "<td>";
				print($value);
				echo "<td>";
				echo "</tr>";
			}
		?>
	</table>
</div>