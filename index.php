<!-- NO MINIMUM DOCUMENT!!!!! -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Gourmet Guru Order Form">
        <link rel="stylesheet" href="styles/style.css">
    </head>
    <body>
        <script>
            window.onload = function() {
                var i = 0;
                var input_read = document.getElementsByName('amount_arr[]');
                while (i < input_read.length) {
                    input_read[i].value = "";
                    document.getElementById('total_costs' + i).style.backgroundColor = "transparent";
                    i += 1;
                }
            };
        </script>

        <?php
            if (isset($_POST['submit'])) {
                $to = "emilymhocking@gmail.com";
                $subject = "My subject";
                $txt = "Hello world!";
                $headers = "From: emilymhocking@gmail.com" . "\r\n";

                mail($to,$subject,$txt,$headers);
            }

            $itemInfo = fopen("items.txt", "r") or die("Unable to get item information.");

            #while(($line = fgetcsv($itemInfo, 0, ":")) !== false) {
            #    $items[] = $line[1];
            #} via https://stackoverflow.com/questions/51195004/how-to-extract-specific-text-from-a-text-file-in-php

            while(($items[] = fgetcsv($itemInfo, 0, ":")) !== FALSE) {}
            
            #echo $items[1][0];
            #echo $items[1][1];
            #echo $items[1][2];

            fclose($itemInfo);

            function remove_headings($array, $key, $value) {
                foreach($array as $subKey => $subArray) {
                    if($subArray[$key] == $value) {
                        unset($array[$subKey]);
                    }
                }
                return $array;
            }

            function create_item_table($items) {
                echo "<div class=\"items\">";
                    echo "<fieldset>";
                        echo "<table>\n";
                            echo "<tr>\n "
                                ."<th>ITEMS</th>\n "
                                ."<th class=\"add_th\">COST PER PIECE</th>\n "
                                ."<th class=\"add_th\">AMOUNT REQUIRED</th>\n "
                                ."<th class=\"add_th\">TOTAL COST</th>\n "
                                ."</tr>\n ";

                            $index = 0;
                            $td_count = 0;
                            $count = count($items);

                            if ($items[$index]) {
                                while ($index < $count) {
                                    if ($items[$index] != "") {
                                        echo "<tr>\n ";

                                        $alt_iterate = 0;
                                        $inner_count = count($items[0]) + 1;

                                        $cost_id = "total_costs" . $td_count;

                                        while ($alt_iterate < $inner_count) {
                                            $found = false;
                                            if ($alt_iterate == 0 && $items[$index][2] != "HEADING") {
                                                echo "<td class=\"item_name\">", trim($items[$index][$alt_iterate]), "</td>\n ";
                                            }
                                            elseif ($alt_iterate == 1 && $items[$index][2] != "HEADING") {
                                                echo "<td>&#36;", trim($items[$index][$alt_iterate]), "</td>\n ";
                                            }
                                            elseif (($alt_iterate == 2 && $items[$index][2] != "HEADING")) {
                                                echo "<td><input type=\"number\" name=\"amount_arr[]\" value=\"\" oninput=\"calculate_total();\"></td>";
                                            }
                                            //&#36;", bcdiv(trim($items[$index][($alt_iterate - 1)]), 1, 2), " - old code note down for bcdiv
                                            elseif ($alt_iterate == 3 && $items[$index][2] != "HEADING") {
                                                echo "<td id=\"", $cost_id, "\"></td>\n ";
                                                $found = true;
                                            }
                                            elseif ($items[$index][2] == "HEADING") {
                                                if ($alt_iterate == 0) {
                                                    echo "<td class=\"item_heading\" colspan=\"4\">", trim($items[$index][$alt_iterate]), "</td>\n ";
                                                }
                                            }
                                            
                                            if ($found == true) {
                                                $td_count += 1;
                                            }
                                            $alt_iterate += 1;
                                        }
                                    }
                                    $index += 1;
                                }
                                echo "</tr>";
                            }
                            echo "<tr>"
                                    ."<td colspan=\"3\" class=\"total_heading\">Total cost:</td>"
                                    ."<td id=\"total_cost\"></td>"
                                ."</tr>";
                        echo "</table>\n ";
                    echo "</fieldset>";
                echo "</div>";
            }
            echo "<form id=\"order_form\" name=\"order_form\" method=\"post\" action=\"\">";
                unset($items[(count($items) - 1)]);
                create_item_table($items);
                $items = remove_headings($items, "2", "HEADING");
                $items = array_values($items);
                //print_r($items);
                echo "<button class=\"button button1\" type=\"button\" onclick=\"get_verify();\">CONFIRM</button>";
                
                //prevent form submit from enter
                echo "<button type=\"submit\" disabled style=\"display: none\" aria-hidden=\"true\"></button>";

                //form submit frfr
                echo "<button class=\"unable\" id=\"submit\" name=\"submit\" disabled type=\"submit\">SUBMIT</button>";
            echo "</form>";
        ?>
        
        <script type="text/javascript">
            var items_mapped;
            var email_arr;
            var updating_arr;

            function calculate_total($items) {
                //document.getElementById('testing').innerHTML = count;
                disable_submit();
                var input_read = document.getElementsByName('amount_arr[]');
                var k = [];
                var index_position_discovery = [];
                var found = false;
                var true_false = [];
                var roll_count = [];
                var sushi_count = [];

                var final_arr = [];

                var i = 0;

                while (i < input_read.length) {
                    var a = input_read[i].value;
                    index_position_discovery.push(i);
                    
                    if (a <= 0) {
                        input_read[i].value = "";
                        a = "";
                        document.getElementById('total_costs' + index_position_discovery[i]).style.backgroundColor = "transparent";
                    }
                    k.push(a);

                    i += 1;
                }

                var items = <?php echo json_encode($items); ?>;
                var indiv_cost = 0;

                var j = 0;

                while (j < input_read.length) {
                    indiv_cost = items[index_position_discovery[j]][1];
                    items[index_position_discovery[j]][2] = indiv_cost * k[j];
                    if (items[index_position_discovery[j]][2] > 0) {
                        document.getElementById('total_costs' + index_position_discovery[j]).innerHTML = "&#36;" + items[index_position_discovery[j]][2].toFixed(2);
                    }
                    else {
                        document.getElementById('total_costs' + j).innerHTML = "";
                    }

                    var item_limit = parseInt(items[index_position_discovery[j]][3]);

                    var item_limit_rs = items[index_position_discovery[j]][3];

                    var count_holder;

                    if (k[j] > 0) {
                        /*
                        if (item_limit != 0) {
                            if (item_limit_rs.includes("c")) {
                                if (item_limit_rs == "40c") {
                                    roll_count.push([k[j], index_position_discovery[j]]);
                                    count_holder = roll_count;
                                    var value_correct = "r";
                                    var value_incorrect = "h";
                                }
                                else if (item_limit_rs == "15c") {
                                    sushi_count.push([k[j], index_position_discovery[j]]);
                                    count_holder = sushi_count;
                                    var value_correct = "s";
                                    var value_incorrect = "y";
                                }

                                var total = 0;
                                for (var c = 0, n = count_holder.length; c < n; c++) {
                                    total += parseInt(count_holder[c][0]);
                                }

                                var n = count_holder.length;
                                var c = 0;

                                if (total > (item_limit - 1)) {
                                    while (c < n) {
                                        document.getElementById('total_costs' + count_holder[c][1]).style.backgroundColor = "lightgreen";
                                        true_false.push(value_correct);
                                        final_arr.push([k[j], index_position_discovery[j]]);
                                        c += 1;
                                    }
                                }
                                else {
                                    while (c < n) {
                                        document.getElementById('total_costs' + count_holder[c][1]).style.backgroundColor = "lightcoral";
                                        true_false.push(value_incorrect);
                                        c += 1;
                                    }
                                }
                            }
                            else if (item_limit > k[j]) {
                                document.getElementById('total_costs' + index_position_discovery[j]).style.backgroundColor = "lightcoral";
                                true_false.push("f");
                            }
                            else {
                                document.getElementById('total_costs' + index_position_discovery[j]).style.backgroundColor = "lightgreen";
                                final_arr.push([k[j], index_position_discovery[j]]);
                                true_false.push("t");
                            }
                        }
                        else {
                            document.getElementById('total_costs' + index_position_discovery[j]).style.backgroundColor = "lightgreen";
                            final_arr.push([k[j], index_position_discovery[j]]);
                            true_false.push("t");
                        }
                        */

                        document.getElementById('total_costs' + index_position_discovery[j]).style.backgroundColor = "lightgreen";
                            final_arr.push([k[j], index_position_discovery[j]]);
                            true_false.push("t");
                    }
                    else if (k[j] == "") {
                        document.getElementById('total_costs' + index_position_discovery[j]).style.backgroundColor = "transparent";
                    }
                    j += 1;
                }

                items_mapped = true_false;
                email_arr = final_arr;
                updating_arr = items;

                console.log("prrir " + items_mapped);

                let total_cost = 0;


                let index = 0;
                while (index < email_arr.length) {
                    cost_calculation(index);
                    index += 1;
                }

                function cost_calculation(index) {
                    //total_cost += cost[1];
                    //total_cost += parseFloat(updating_arr[email_arr[index][1]][1].trim());
                    total_cost += parseFloat(updating_arr[email_arr[index][1]][2]);
                }
                console.log(total_cost);

                //document.getElementById('testing').innerHTML = k;
                //document.getElementById('id').innerHTML = index_position_discovery;
                //document.getElementById('two').innerHTML = final_arr;

                
                document.getElementById('total_cost').innerHTML = "&#36;" + total_cost.toFixed(2);
            }

            function disable_submit() {
                submit.disabled = true;
                let element = document.getElementById("submit");
                element.className = "unable";
                document.getElementById('one').innerHTML = "";
            }

            function get_verify() {
                create_email();
                index = 0;
                count = items_mapped.length;
                while (index < count) {
                    if (items_mapped.includes("f")) {
                        submit.disabled = true;
                        let element = document.getElementById("submit");
                        element.className = "unable";
                    }
                    else {
                        submit.disabled = false;
                        let element = document.getElementById("submit");
                        element.className = "button button1";
                    }

                    if (items_mapped.includes("h") && items_mapped.includes("r") == false) {
                        submit.disabled = true;
                        let element = document.getElementById("submit");
                        element.className = "unable";
                    }

                    if (items_mapped.includes("y") && items_mapped.includes("s") == false) {
                        submit.disabled = true;
                        let element = document.getElementById("submit");
                        element.className = "unable";
                    }
                    index += 1;
                }
            }

            function create_email() {
                var email;
                var order_item = "";
                index = 0;
                count = email_arr.length;
                while (index < count) {
                    order_item = order_item + email_arr[index][0] + " x " + updating_arr[email_arr[index][1]][0] + "\r\n&#36;" + updating_arr[email_arr[index][1]][2].toFixed(2) + " (&#36;" + updating_arr[email_arr[index][1]][1].trim() + " each)\r\n\r\n";
                    index += 1;
                }

                order_item = order_item + "Total Cost\r\n" + document.getElementById('total_cost').innerHTML + " (incl GST)";
                
                document.getElementById('one').innerHTML = order_item;

                //document.getElementById('tt').innerHTML = "Total Cost";
                //document.getElementById('totalprice').innerHTML = document.getElementById('total_cost').innerHTML + " (incl GST)";
            }
        </script>
        <!--
        <p id="record">hehe</p>
        <p id="testing">hehe</p>
        <p id="diff">hehe</p>
        <p id="id">hehe</p>
        -->

        <pre id="one" style="font-size: 14.5px; font-family: 'Calibri'"></pre>
        <p id="tt"></p>
        <p id="totalprice"></p>
        <!--<p id="two"></p>-->
    </body>
</html>