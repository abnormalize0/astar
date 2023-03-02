<?php
define ("NOT_VISITED", 0);
define ("SEEN", 1);
define ("VISITED", 2);

function initial_table($maze, $height, $width) {
    echo "<table>"."\n";
	for($i = 0; $i < $height; $i++) {
        echo "<tr>"."\n";
		for($j = 0; $j < $width; $j++) {
		    echo "<td><div class='action' onclick='setpoint(".$i.", ".$j.", ".conv($i, $j, $height).")' id='cell".conv($i, $j, $height). "'>"."\n";
			echo($maze[$i][$j]);
            echo "</div></td>"."\n";
		}
        echo "</tr>"."\n";
	}
    echo "</table>"."\n"."<br>"."<br>";
}

function path_table($maze, $cell, $initial, $height, $width) {
    $current_cell = $initial;
    $path = array();
    array_push($path, "-1");
    array_push($path, $current_cell);
    while ($cell[$current_cell]["previous"] != -1) {
        $current_cell = $cell[$current_cell]["previous"];
        array_push($path, $current_cell);
    }
    echo "<table>"."\n";
	for($i = 0; $i < $height; $i++) {
        echo "<tr>"."\n";
		for($j = 0; $j < $width; $j++) {
		    if (array_search(conv($i, $j, $height), $path) != "") {
		        echo "<td><div class='action' style='background-color: #45A29E;' onclick='setpoint(".$i.", ".$j.", ".conv($i, $j, $height).")' id='cell".conv($i, $j, $height). "'>"."\n";
		    } else {
		        echo "<td><div class='action' onclick='setpoint(".$i.", ".$j.", ".conv($i, $j, $height).")' id='cell".conv($i, $j, $height). "'>"."\n";
		    }
			echo($maze[$i][$j]);
            echo "</div></td>"."\n";
		}
        echo "</tr>"."\n";
	}
    echo "</table>"."\n"."<br>"."<br>";
}

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
	return sqrt($deltax ** 2 + $deltay ** 2);
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

function main() {
    session_start();
    $cycle = 0;
    if (!isset($_COOKIE["state"])) {
        setcookie("state", "0", time() + 3600);
        return;
    }
    
    $maze = array();
    $height = 0;
    $width = 0;
    if ($_COOKIE["state"] == "0") {
        if (isset($_SESSION['file'])) {
            $analyze = $_SESSION['file'];
        } else {
            $myfile = fopen("uploads/".$_COOKIE["file"], "r");
            $analyze = fread($myfile,filesize("uploads/".$_COOKIE["file"]));
            fclose($myfile); 
            unlink("uploads/".$_COOKIE["file"]);
            $_SESSION['file'] = $analyze;
        }
		if ($analyze == 0) {
            return;
        }
        $rows = explode("\n", $analyze);
        foreach($rows as &$item) {
            if (count(explode(" ", $item)) != 1) {
                array_push($maze, explode(" ", $item));
            }
        }
        $height = count($maze);
        $width = count($maze[0]);
        if (($height == 1) && ($width == 1)) {
            echo("<div class='warning'>Некорректный ввод</div>");
            return;
        }
        for ($i = 1; $i < count($maze); $i++) {
            if (count($maze[$i]) != count($maze[0])) {
                echo("<div class='warning'>Некорректный ввод</div>");
                return;
            }
        }
        setcookie("point1x", "-1", time() + 3600);
        setcookie("point1y", "-1", time() + 3600);
        setcookie("point2x", "-1", time() + 3600);
        setcookie("point2y", "-1", time() + 3600);
        setcookie("state", "1", time() + 3600);
        initial_table($maze, $height, $width);
        return;
    }
    
    $current_x = 0;
    $current_y = 0;
    $answer_x = 0;
    $answer_y = 0;
    if ($_COOKIE["state"] == "1") {
        if (isset($_SESSION['file'])) {
            $analyze = $_SESSION['file'];
        } else {
            $myfile = fopen("uploads/".$_COOKIE["file"], "r");
            $analyze = fread($myfile,filesize("uploads/".$_COOKIE["file"]));
            fclose($myfile); 
            unlink("uploads/".$_COOKIE["file"]);
            $_SESSION['file'] = $analyze;
        }
        $rows = explode("\n", $analyze);
        foreach($rows as &$item) {
            if (count(explode(" ", $item)) != 1) {
                array_push($maze, explode(" ", $item));
            }
        }
        $height = count($maze);
        $width = count($maze[0]);
        $current_x = $_COOKIE["point1x"];
        $current_y = $_COOKIE["point1y"];
        $answer_x = $_COOKIE["point2x"];
        $answer_y = $_COOKIE["point2y"];
        if ($current_x == -1) {
            initial_table($maze, $height, $width);
            return;
        }
        setcookie("point1x", "-1", time() + 3600);
        setcookie("point1y", "-1", time() + 3600);
        setcookie("point2x", "-1", time() + 3600);
        setcookie("point2y", "-1", time() + 3600);
        $cycle = 1;
    }
    
    $cell = array();
    $seen_cells = array();
    for($i = 0; $i < $height * $width; $i++) {
    	array_push($cell, array('from_start' => 0, 'to_end' => 0, 'previous' => -1, 'state' => 0));
    }
    
    if ($maze[$current_x][$current_y] == "0") {
        initial_table($maze, $height, $width);
        echo("<div class='warning'>Пути не существует</div>");
        $cycle = 0;
    }
    while($cycle) {
    	if (($current_x > 0)&&($maze[$current_x - 1][$current_y] != 0)) {
    		if ($cell[conv($current_x - 1, $current_y, $height)]['state'] == NOT_VISITED) {
				array_push($seen_cells, conv($current_x - 1, $current_y, $height));
			}
    		$cell = cell_process($cell, conv($current_x, $current_y, $height), 
								$current_x - 1, $current_y, $maze[$current_x - 1][$current_y], 
								$answer_x, $answer_y, $height);
    	} 
    	if (($current_x < $height - 1)&&($maze[$current_x + 1][$current_y] != 0)) {
    		if ($cell[conv($current_x + 1, $current_y, $height)]['state'] == NOT_VISITED) {
				array_push($seen_cells, conv($current_x + 1, $current_y, $height));
			}
    		$cell = cell_process($cell, conv($current_x, $current_y, $height), 
								$current_x + 1, $current_y, $maze[$current_x + 1][$current_y], 
								$answer_x, $answer_y, $height);
    	} 
    	if (($current_y > 0)&&($maze[$current_x][$current_y - 1] != 0)) {
    		if ($cell[conv($current_x, $current_y - 1, $height)]['state'] == NOT_VISITED) {
				array_push($seen_cells, conv($current_x, $current_y - 1, $height));
			}
    		$cell = cell_process($cell, conv($current_x, $current_y, $height), 
							$current_x, $current_y - 1, $maze[$current_x][$current_y - 1], 
							$answer_x, $answer_y, $height);
    	} 
    	if (($current_y < $width - 1)&&($maze[$current_x][$current_y + 1] != 0)) {
    		if ($cell[conv($current_x, $current_y + 1, $height)]['state'] == NOT_VISITED) {
				array_push($seen_cells, conv($current_x, $current_y + 1, $height));
			}
    		$cell = cell_process($cell, conv($current_x, $current_y, $height), 
							$current_x, $current_y + 1, $maze[$current_x][$current_y + 1], 
							$answer_x, $answer_y, $height);
    	} 
    	if (count($seen_cells) == 0) {
    	    initial_table($maze, $height, $width);
    	    echo("<div class='warning'>Пути не существует</div>");
    	    break;
    	}
    	$new_best = $seen_cells[0];
    	foreach($seen_cells as &$value) {
    		if ($cell[$value]['from_start'] + $cell[$value]['to_end'] < $cell[$new_best]['from_start'] + $cell[$new_best]['to_end']) {
    			$new_best = $value;
    		} else if (($cell[$value]['from_start'] + $cell[$value]['to_end'] == $cell[$new_best]['from_start'] + $cell[$new_best]['to_end'])
						&&($cell[$value]['to_end'] < $cell[$new_best]['to_end'])) {
    			$new_best = $value;
    		}
    	}
    	
        $best_id = array_search($new_best, $seen_cells);
    	array_splice($seen_cells, $best_id, 1);
        $cell[conv($current_x,$current_y, $height)]['state'] = VISITED;
    	$current_x = break_x($new_best, $height);
    	$current_y = break_y($new_best, $height);
    	if (($current_x == $answer_x) && ($current_y == $answer_y)) {
    		$cycle = 0;
            path_table($maze, $cell, $new_best, $height, $width);
    	}
    }
}

main();
?>