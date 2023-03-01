<?php
setcookie("allow", "0", time() + 3600);
if (isset($_COOKIE["file"])) {
    // print "lang cookie value: " . $_COOKIE["file"];
    $analyze = $_COOKIE["file"];
    setcookie("file", "", time() - 3600);
} else {
    print "lang cookie doesn't exists";
    die();
}

$maze = array();
$rows = explode("\n", $analyze);
foreach($rows as &$item) {
    array_push($maze, explode(" ", $item));
}

define ("NOT_VISITED", 0);
define ("SEEN", 1);
define ("VISITED", 2);

$height = count($maze);
$width = count($maze[0]);

$current_x = 4;
$current_y = 0;

$answer_x = 0;
$answer_y = 0;

function main_table($maze, $height, $width) {
    echo "<table>"."\n";
	for($i = 0; $i < $height; $i++) {
        echo "<tr>"."\n";
		for($j = 0; $j < $width; $j++) {
		    echo "<td><div id='cell".conv($i, $j, $height). "'>"."\n";
			echo($maze[$i][$j]);
            echo "</div></td>"."\n";
		}
        echo "</tr>"."\n";
	}
    echo "</table>"."\n"."<br>"."<br>";
}

// function recolor($cell, $maze, $height, $width) {
//     for($i = 0; $i < $height; $i++) {
// 		for($j = 0; $j < $width; $j++) {
// 		    if ($cell[conv($i,$j,$height)]['state'] == SEEN) {
// 		    ?><script>
//               document.getElementByID("cell" + <?php echo conv($i,$j,$height); ?>).style.backgroundColor = "red";
//             </script><?php
// 		    }
// 		}
// 	}
// }

function conv($x, $y, $height) {
	return $y * $height + $x;
}

function break_x($id, $height) {
	return $id % $height;
}

function break_y($id, $height) {
	return intdiv($id, $height);
}

function straight_line($x1,$y1,$x2,$y2) {
	$deltax = abs($x1 - $x2);
	$deltay = abs($y1 - $y2);
	$f = 0.0;
	$f = $deltax ** 2 + $deltay ** 2;
	return sqrt($deltax ** 2 + $deltay ** 2);
}

function display($cell, $maze, $height, $width) {
    echo "<table>"."\n";
	for($i = 0; $i < $height; $i++) {
        echo "<tr>"."\n";
		for($j = 0; $j < $width; $j++) {
		    if ($cell[conv($i,$j,$height)]['state'] == SEEN) {
		        echo "<td><div style='background-color:green;'>"."\n";
		    } else if ($cell[conv($i,$j,$height)]['state'] == VISITED) {
		        echo "<td><div style='background-color:red;'>"."\n";
		    } else {
		        echo "<td><div style='background-color:white;'>"."\n";
		    }
			echo($maze[$i][$j])." [";
			echo($cell[conv($i, $j, $height)]['from_start'])." ";
			echo($cell[conv($i, $j, $height)]['to_end'])."] ";
            echo "</div></td>"."\n";
		}
        echo "</tr>"."\n";
	}
    echo "</table>"."\n"."<br>"."<br>";
}

function pathfinding($cell, $initial, $height) {
    $current_cell = $initial;
    $path = array();
    array_push($path, $current_cell);
    while ($cell[$current_cell]["previous"] != -1) {
        $current_cell = $cell[$current_cell]["previous"];
        array_push($path, $current_cell);
    }
    $c_path = "";
    foreach($path as &$item) {
        echo break_x($item, $height)." ".break_y($item, $height)."<br>";
        $c_path = $item.".".$c_path;
    }
    setcookie("path", $c_path, time() + 3600);
    setcookie("allow", "1", time() + 3600);
}

function cell_process($cell, $conv_id, $neighbor_x, $neighbor_y, $neighbor_path, $answer_x, $answer_y, $height) {
	$id = conv($neighbor_x, $neighbor_y, $height);
	if ($cell[$id]['state'] == NOT_VISITED) {
		$cell[$id]['from_start'] = $cell[$conv_id]['from_start'] + $neighbor_path;
		$cell[$id]['to_end'] = straight_line($answer_x, $answer_y, $neighbor_x, $neighbor_y);
		$cell[$id]['previous'] = $conv_id;
		$cell[$id]['state'] = SEEN;
	} else if (($cell[$id]['state'] == SEEN)&&($cell[$id]['from_start'] > $cell[$conv_id]['from_start'] + $neighbor_path)) {
		$cell[$id]['from_start'] = $cell[$conv_id]['from_start'] + $neighbor_path;
		$cell[$id]['previous'] = $conv_id;
	}
	return $cell;
}

$cell = array();
$seen_cells = array();
for($i = 0; $i < $height * $width; $i++) {
	array_push($cell, array('from_start' => 0, 'to_end' => 0, 'previous' => -1, 'state' => 0));
}
main_table($maze, $height, $width);
$cycle = 1;
while($cycle) {
	if (($current_x > 0)&&($maze[$current_x - 1][$current_y] != 0)) {
		if ($cell[conv($current_x - 1, $current_y, $height)]['state'] == NOT_VISITED) array_push($seen_cells, conv($current_x - 1, $current_y, $height));
		$cell = cell_process($cell, conv($current_x, $current_y, $height), $current_x - 1, $current_y, $maze[$current_x - 1][$current_y], $answer_x, $answer_y, $height);
	} 
	if (($current_x < $height - 1)&&($maze[$current_x + 1][$current_y] != 0)) {
		if ($cell[conv($current_x + 1, $current_y, $height)]['state'] == NOT_VISITED) array_push($seen_cells, conv($current_x + 1, $current_y, $height));
		$cell = cell_process($cell, conv($current_x, $current_y, $height), $current_x + 1, $current_y, $maze[$current_x + 1][$current_y], $answer_x, $answer_y, $height);
	} 
	if (($current_y > 0)&&($maze[$current_x][$current_y - 1] != 0)) {
		if ($cell[conv($current_x, $current_y - 1, $height)]['state'] == NOT_VISITED) array_push($seen_cells, conv($current_x, $current_y - 1, $height));
		$cell = cell_process($cell, conv($current_x, $current_y, $height), $current_x, $current_y - 1, $maze[$current_x][$current_y - 1], $answer_x, $answer_y, $height);
	} 
	if (($current_y < $width - 1)&&($maze[$current_x][$current_y + 1] != 0)) {
		if ($cell[conv($current_x, $current_y + 1, $height)]['state'] == NOT_VISITED) array_push($seen_cells, conv($current_x, $current_y + 1, $height));
		$cell = cell_process($cell, conv($current_x, $current_y, $height), $current_x, $current_y + 1, $maze[$current_x][$current_y + 1], $answer_x, $answer_y, $height);
	} 
	$new_best = $seen_cells[0];
	foreach($seen_cells as &$value) {
		if ($cell[$value]['from_start'] + $cell[$value]['to_end'] < $cell[$new_best]['from_start'] + $cell[$new_best]['to_end']) {
			$new_best = $value;
		} else if (($cell[$value]['from_start'] + $cell[$value]['to_end'] == $cell[$new_best]['from_start'] + $cell[$new_best]['to_end'])&&($cell[$value]['to_end'] < $cell[$new_best]['to_end'])) {
			$new_best = $value;
		}
	}
	$best_id = array_search($new_best, $seen_cells);
	array_splice($seen_cells, $best_id, 1);
    $cell[conv($current_x,$current_y, $height)]['state'] = VISITED;
	$current_x = break_x($new_best, $height);
	$current_y = break_y($new_best, $height);
// 	display($cell, $maze, $height, $width);
	if (($current_x == $answer_x) && ($current_y == $answer_y)) {
		$cycle = 0;
        pathfinding($cell, $new_best, $height);
	} else {
	    $cycle++;
	}
}
    
?>