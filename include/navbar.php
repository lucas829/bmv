<?
function navbar($nav=true) {
    if($nav) {
        global $menu;
	echo "<div id='menu'>\n";
        echo "<ul class='navbar'>\n";
	for($i=0;$i<sizeof($menu);$i++){
                echo "<li class='nivel1'>";
		if(is_array($menu[$i][1])){	# Si es array, tenemos sub-menus
			echo "<span class='nivel1_static'>".$menu[$i][0]."</span>\n";
			echo "<ul>\n";
			for($j=1;$j<sizeof($menu[$i]);$j++) {
				echo "<li>";
				echo "<a href='{$menu[$i][$j][1]}'>{$menu[$i][$j][0]}</a>";
				echo "</li>\n";
			}
			echo "</ul>\n";
			echo "</li>";
		} else {	# Si no es array, el menú es link
		echo "<a href='{$menu[$i][1]}' class='nivel1'>{$menu[$i][0]}</a>";
                echo "</li>\n";
		}
        }
        echo "</ul>\n";
	echo "</div>\n";
	echo "<div class='clear'></div>";
    }
}
