<div class="templatepeopledataimgcontainer">

    <!-- <img src="<?php // echo get_stylesheet_directory_uri() . '/templates/loading.gif'; 
                    ?>" width="250px;" /> -->
</div>


<?php
//quiz_details, quiz_user_details, learning_sections
global $wpdb;
$table_name = $wpdb->prefix . 'quiz_details';
$quiz_table_name = $wpdb->prefix . 'quiz_user_details';
$quiz_section_score = $wpdb->prefix . 'quiz_section_score';
$q1 = get_users();

$sp = array();
foreach ($q1 as $key => $value) {
    $learning_modules = get_user_meta($value->ID, 'learning_modules_progress', true);
    $learning_modules = unserialize($learning_modules);

    if (!empty($learning_modules)) {
        foreach ($learning_modules as $lk => $lvalue) {

            //  $isassigned = $learning_modules[$lk];
            if ($lvalue['active'] == 1) {  //$isassigned['is_all_complated'] == 1  && 
                $sp[$value->ID][$lvalue['id']] = [];
                $score_results = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM `wp_quiz_user_details` AS qud INNER JOIN `wp_quiz_details` AS qd ON qd.quizid = qud.quizid WHERE qud.userid = {$value->ID} order BY qud.userid, qd.sectionid", ARRAY_A);
                if (!empty($score_results)) {

                    foreach ($score_results as $sk => $svalue) {

                        $pscore = json_decode($svalue['score']);
                        $score = $pscore->percentage;
                        $quiz_score = round($score);

                        $sp[$svalue['userid']][$svalue['sectionid']][] = $quiz_score;
                    }
                } else {

                    $sp[$value->ID][$lvalue['id']][] = '';
                }
            }
        }
    }
}
// commented below code and uncommented above code by kashif watto at 18 april 2024

//  $q1 = $wpdb->get_results("SELECT qud.score,qud.userid,qud.quizid,qd.sectionid FROM `wp_quiz_user_details` AS qud INNER JOIN `wp_quiz_details` AS qd ON qd.quizid = qud.quizid order BY qud.userid, qd.sectionid",ARRAY_A );
// $sp = array();
// foreach ($q1 as $key => $value) {  
//     $pscore =  json_decode($value['score']);
//     $score = $pscore->percentage; 
//     $quiz_score = round($score);
//     $learning_modules = get_user_meta($value['userid'] , 'learning_modules_progress', true);
//     $learning_modules = unserialize($learning_modules);
//     $isassigned = $learning_modules[$value['sectionid']];                
//     // if($isassigned['is_all_complated'] == 1 &&  $isassigned['active'] == 1 ){  
//     if(true){  
//         $sp[$value['userid']][$value['sectionid']][] = $quiz_score;
//     }

// } 
?>

<div class="learning-main-section">
    <div class="learning-top-header People-top-header">
        <h1>Staff</h1>
    </div>
    <div class="sub-section">
        <div class="sub-sec-left">
            <h3>
                <?php echo "Name"; ?>
            </h3>
        </div>
        <div class="sub-sec-right">
            <h3>
                <?php echo "Score"; ?>
            </h3>
        </div>
    </div>
    <div class="people-list">
        <ul>
            <?php foreach ($sp as $key => $value) {

                $display_name = mystaff_training_staff_training_get_display_name($key);
                $name = $display_name;
                $user_id = $key;
                $myUser = get_userdata($user_id);
                $user_cat = $myUser->user_cat;

                $weight_arr = array();
                if ($name) { ?>
                    <li>

                        <div class="people-name people-name-custom">
                            <div class="pro-percentage">
                                <a class="scoreinfo" data-toggle="modal" data-target="#myModal-all-<?php echo esc_attr($user_id); ?>">
                                    <h3 class="name-info"><?php echo esc_html($name); ?></h3>
                                </a>
                                <?php
                                $allsection_array = array();
                                foreach ($value as $sid => $sval) {
                                    $learning_modules_new = get_user_meta($user_id, 'learning_modules_progress', true);
                                    $learning_modules_new = unserialize($learning_modules_new);
                                    if (!empty($learning_modules_new[$sid]['pages'])) {

                                        $total_steps = 0;
                                        $total_completed_step = 0;

                                        if ($learning_modules_new[$sid]['is_all_complated'] == 1 && $learning_modules_new[$sid]['active'] == 1) {
                                            $score = $value[$sid];
                                            if (!empty($score)) :
                                                $t_quiz_score = array_sum($score) / count($score);
                                            endif;

                                            $allsection_array[$sid] = array('score' => round($t_quiz_score), 'completed' => 'yes');
                                        } else {


                                            foreach ($learning_modules_new[$sid]['pages'] as $key1 => $learning_subsec) {
                                                $complated_class = '';
                                                if ($learning_subsec['status'] == 'completed') {
                                                    $total_completed_step++;
                                                }
                                                $total_steps++;
                                            }

                                            $pro_percentage = 0;
                                            if ($total_completed_step > 0) {
                                                $pro_percentage = 100 / $total_steps;
                                                $pro_percentage = $pro_percentage * $total_completed_step;
                                            }
                                            $allsection_array[$sid] = array('score' => round($pro_percentage), 'completed' => 'no');
                                        }
                                    }
                                }
                                //$allsection_array = wp_list_sort( $allsection_array, 'score', 'ASC');
                                asort($allsection_array);

                                echo esc_html(mystaff_training_new_popupfunction('all', $allsection_array, $user_id, $name));

                                // $allsection_array = array();
                                //  foreach($value as $sid => $sval) {  
                                //     $score = $value[$sid];                          
                                //     if(!empty($score)) :
                                //         $t_quiz_score = array_sum($score) / count($score);
                                //     endif;

                                //     $allsection_array[$sid] = round($t_quiz_score);
                                // }
                                // asort( $allsection_array );
                                // echo popupfunction('all', $allsection_array ,$user_id,$name); 

                                ?>
                            </div>
                        </div>
                        <div class="people-per-wrapper people-per-wrapper-custom-1 ">
                            <select class="sub-sec-right" name="user_cat" id="user_cat" data-user-id="<?php echo esc_attr($user_id); ?>">
                                <option value="All">All</option>
                                <option value="Installation" <?php echo ($user_cat === 'Installation') ? 'selected' : ''; ?>>
                                    Installation
                                </option>
                                <option value="Maintenance" <?php echo ($user_cat === 'Maintenance') ? 'selected' : ''; ?>>
                                    Maintenance
                                </option>
                            </select>
                        </div>
                        <div class="people-per-wrapper  people-per-wrapper-custom-2">
                            <?php
                            $gold = $silver = $bronze = $fail = 0;
                            $g_arr = $s_arr = $b_arr = $f_arr = array();
                            foreach ($value as $sid => $sval) {

                                $learning_modules_new = get_user_meta($user_id, 'learning_modules_progress', true);
                                $learning_modules_new = unserialize($learning_modules_new);
                                $isassigned_new = $learning_modules_new[$sid];
                                if ($isassigned_new['is_all_complated'] == 1 && $isassigned_new['active'] == 1) {

                                    $score = $value[$sid];
                                    if (!empty($score)) :
                                        $t_quiz_score = array_sum($score) / count($score);
                                    endif;

                                    $quiz_score = round($t_quiz_score);

                                    if ($quiz_score == get_option('gold_score_min')) :
                                        $gold += 1;
                                        $g_arr[$sid] = round($quiz_score);
                                    elseif ($quiz_score >= get_option('silver_score_min') && $quiz_score <= (get_option('gold_score_min') - 1)) :
                                        $silver += 1;
                                        $s_arr[$sid] = round($quiz_score);
                                    elseif ($quiz_score >= get_option('bronze_score_min') && $quiz_score <= (get_option('silver_score_min') - 1)) :
                                        $bronze += 1;
                                        $b_arr[$sid] = round($quiz_score);
                                    elseif ($quiz_score >= get_option('fail_score_min') && $quiz_score <= (get_option('bronze_score_min') - 1)) :
                                        $fail += 1;
                                        $f_arr[$sid] = round($quiz_score);
                                    endif;
                                }
                                // $getweightofuser = $wpdb->get_results("SELECT * FROM {$quiz_section_score} WHERE userid={$user_id} AND sectionid={$sid}",ARRAY_A);
                                // foreach($getweightofuser as $wght){
                                //     $weight_arr[] = $wght['score_weight'];
                                // }
                            }
                            ?>

                            <?php if ($gold > 0) : ?>
                                <div class="pro-percentage">
                                    <a class="scoreinfo" data-toggle="modal" data-target="#myModal-gold-<?php echo esc_attr($user_id); ?>">
                                        <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder . '/images/trophygold.png'); ?>" width="25px;" />
                                        <p><?php echo esc_html($gold); ?></p>
                                    </a>
                                    <?php echo esc_html(mystaff_training_new_popupfunction('gold', $g_arr, $user_id, $name)); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($silver > 0) : ?>
                                <div class="pro-percentage">
                                    <a class="scoreinfo" data-toggle="modal" data-target="#myModal-silver-<?php echo esc_attr($user_id); ?>">
                                        <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder . '/images/trophysilver.png'); ?>" width="25px;" />
                                        <p><?php echo esc_html($silver); ?></p>
                                    </a>
                                    <?php echo esc_html(mystaff_training_new_popupfunction('silver', $s_arr, $user_id, $name)); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($bronze > 0) : ?>
                                <div class="pro-percentage">
                                    <a class="scoreinfo" data-toggle="modal" data-target="#myModal-bronze-<?php echo esc_attr($user_id); ?>">
                                        <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder . '/images/trophybronze.png'); ?>" width="25px;" />
                                        <p><?php echo esc_html($bronze); ?></p>
                                    </a>
                                    <?php echo esc_html(mystaff_training_new_popupfunction('bronze', $b_arr, $user_id, $name)); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($fail > 0) : ?>
                                <div class="pro-percentage">
                                    <a class="scoreinfo" data-toggle="modal" data-target="#myModal-fail-<?php echo esc_attr($user_id); ?>">
                                        <img src="<?php echo esc_url(mystaff_training_plugin_dir_folder . '/images/trophyx.png'); ?>" width="25px;" />
                                        <p><?php echo esc_html($fail); ?></p>
                                    </a>
                                    <?php echo esc_html(mystaff_training_new_popupfunction('fail', $f_arr, $user_id, $name)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
            <?php
                } //if name

            } //main foreach
            ?>
        </ul>
    </div>
</div>

<!-- <a id="customTestBtn"><button> Test Function</button></a> -->



<!-- Modal -->


<script>
    jQuery('.cc-list .quizresultinfo').on('click', function() {
        jQuery(this).siblings('.acc-content ').slideToggle();
    });
</script>
<script>
    jQuery(document).ready(function($) {
        jQuery(document).on('change', '#user_cat', function() {
            var cat_value = jQuery(this).val();
            var user_id = jQuery(this).data('user-id');

            jQuery.ajax({

                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                type: 'post',

                data: {
                    action: "mystaff_training_staff_training_update_user_cat",
                    cat_value: cat_value,
                    user_id: user_id,
                },

                success: function(data) {
                    if (data == 'assigned') {} else {
                        console.log("not updatedd");
                    }

                }

            });

        });
    });
</script>

<?php
function mystaff_training_new_popupfunction($string, $trophy_arr, $userid, $name)
{
?>
    <div class="modal fade modal-xl" id="myModal-<?php echo esc_attr($string . '-' . $userid); ?>" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo esc_html($name) . ' - ';
                                            if ($string == 'all') {
                                                echo 'Courses';
                                            } else {
                                                echo esc_html($string);
                                            } ?></h4>


                </div>
                <div class="modal-body">
                    <ul>
                        <?php
                        global $wpdb;
                        foreach ($trophy_arr as $id => $val) {

                            $detail = mystaff_training_staff_training_get_specific_section_by_id($id);
                            $learning_subsection = unserialize($detail->learning_subsection);

                            // Extract sub_completed_url values
                            $sub_completed_urls = array();
                            $sub_title = array();
                            $quiz_ids = array();
                            foreach ($learning_subsection as $subsection) {
                                $sub_completed_urls[] = $subsection['sub_completed_url'];
                                $sub_title[] = $subsection['sub_title'];
                            }
                            foreach ($sub_title as $subtitle) {
                                $subtitle = $id . '_' . $subtitle;
                                $quiz_id = $wpdb->get_var(
                                    $wpdb->prepare(
                                        "SELECT quizid FROM {$wpdb->prefix}quiz_details WHERE sectionid = %d AND subsection_title = %s",
                                        $id,
                                        $subtitle
                                    )
                                );
                                $quiz_ids[] = $quiz_id;
                            }

                            if ($detail) {
                        ?>
                                <li class="cc-list">


                                    <a style="width:30%; text-decoration:none" class="quizresultinfo" href="javascript:void(0);">
                                        <h3><?php echo esc_html($detail->title);; ?></h3>
                                    </a>


                                    <?php
                                    if (is_array($val)) {
                                        if ($userid == '19') {
                                            // error_log('code');
                                            //  error_log(json_encode($val));
                                        }


                                        // if($val['completed'] == 'no') { old condition
                                        if ($val['completed'] == 'no') {
                                            $clsclr = 'red';
                                            $width = round($val['score']) . '%';
                                        } else {
                                            $clsclr = 'green';
                                            $width = '100%';
                                        }
                                        echo '<div class="progress-section">';
                                        echo '<div class="bar-top">';
                                        echo '<div class="pro-percentage">';
                                        echo '<span>' . esc_html($val['score']) . '</span>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="pro-percentage-bar">';
                                        echo '<div class="current-progress ' . esc_attr($clsclr) . '" style="width:' . esc_attr($width) . '"></div>';
                                        echo '</div>';
                                        echo '</div>';

                                        if (!($val['score'] == 0)) { ?>
                                            <button class="btn btn-default redo-course-staffpage" data-userid="<?php echo esc_attr($userid); ?>" data-sectionid="<?php echo esc_attr($detail->id); ?>" onclick="restquizeestousers(this)">Reset</button>
                                        <?php
                                        } else {

                                        ?>
                                            <button class="btn btn-default " data-userid="<?php echo $userid; ?>" data-sectionid="<?php echo $detail->id; ?>" data-quizdata="<?php echo htmlspecialchars(json_encode($quiz_ids));  ?>" data-completedurl="<?php echo htmlspecialchars(json_encode($sub_completed_urls));  ?>" onclick="autopassfunction(this)">Auto Pass</button>

                                        <?php
                                        }

                                        ?>

                                    <?php

                                    } else {
                                        echo '<span>' . esc_html($val) . '</span>';
                                        // echo get_wrong_answer_list($userid,$id);

                                    }

                                    ?>

                                    <div class="acc-content">

                                        <?php
                                        // add condition to check if section is completed or not by 22/05/2024 3:34PM
                                        echo esc_html(mystaff_training_get_wrong_answer_list($userid, $id));

                                        if (!($val['completed'] == 'no')) {
                                        } else {
                                            // echo "Not complete yet";
                                        }
                                        ?>
                                    </div>



                                </li>

                        <?php
                            }
                        } //for each value as sid   
                        ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php
}
?>


<script>
    function restquizeestousers(button) {
        var userid = jQuery(button).data('userid'); // Access data attribute from the button element
        var sectionid = jQuery(button).data('sectionid');
        console.log(userid);
        console.log(sectionid);
        Swal.fire({
            title: 'Are you sure you want to reset this course?',
            text: "This will erase current score.",
            icon: 'question',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset course!',
            showCancelButton: true,
        }).then((result) => {

            if (result.isConfirmed) {

                jQuery.ajax({
                    url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                    type: "POST",
                    data: {
                        action: 'mystaff_training_redo_course_module_for_admin',
                        sectionid: sectionid,
                        userid: userid,
                    },
                    success: function(data) {
                        location.reload();

                    }
                });

            }

        });

    }
</script>

<script>
    function autopassfunction(button) {
        var userid = jQuery(button).data('userid'); // Access data attribute from the button element
        var sectionid = jQuery(button).data('sectionid');
        var quizdata = jQuery(button).data('quizdata');
        var sectionurl = jQuery(button).data('completedurl');
        if (!Array.isArray(quizdata) || !Array.isArray(sectionurl) || quizdata.length !== sectionurl.length) {
            console.error('quizdata and sectionurl must be arrays of the same length');
            return;
        }

        Swal.fire({
            title: 'Are you sure you want to auto pass this course?',
            text: "This will complete this course for this user and increase coins.",
            icon: 'question',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Autopass course!',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                var overlay = jQuery('<div id="overlayemail"> Please Wait... </div>');
                jQuery('body').append(overlay);
                var ajaxRequests = [];

                var initialRequest = jQuery.ajax({
                    url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                    type: 'POST',
                    data: {
                        action: 'mystaff_training_function_check_leaderboarddata_before_pass', // Your chekcustom action name

                    },
                    success: function(response) {

                        console.log('Initial request sent');
                        for (var i = 0; i < quizdata.length; i++) {
                            var currentQuizData = quizdata[i];
                            var currentSectionUrl = sectionurl[i];


                            var request = jQuery.ajax({
                                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                                type: 'POST',
                                data: {
                                    action: 'mystaff_training_learning_modules_save_action_for_autopass_from_admin',
                                    sectionid: sectionid,
                                    userid: userid,
                                    pagelink: currentSectionUrl, // Send the sectionurl with each request
                                    quizid: currentQuizData // Send the quizdata with each request
                                },



                            });
                            ajaxRequests.push(request);
                        }

                        // When all AJAX requests are done, reload the page
                        jQuery.when.apply(jQuery, ajaxRequests).done(function() {
                            console.log('request done');
                            var request2 = jQuery.ajax({
                                url: '<?php echo esc_url(site_url('/wp-admin/admin-ajax.php')); ?>',
                                type: 'POST',
                                data: {
                                    action: 'mystaff_training_learning_modules_save_action_for_autopass_from_admin_request2',
                                    sectionid: sectionid,
                                    userid: userid,
                                    olddata: response,
                                    quizid: quizdata // Send the quizdata with each request
                                },
                                success: function() {
                                    console.log('email sent');
                                    location.reload();


                                }

                            });
                            ajaxRequests.push(request2);
                            // jQuery.when.apply(jQuery, ajaxRequests).done(function() {
                            //     console.log('email sent');
                            //     location.reload();

                            // });
                        });
                    }
                });
                ajaxRequests.push(initialRequest);
                // Iterate over the arrays and send AJAX requests

            }
        });
    }
</script>


<script>
    // $(document).ready(function() {
    //     $(document).on('click', '#customTestBtn', function() {


    //         jQuery.ajax({

    //             url: '<?php echo site_url(); ?>/wp-admin/admin-ajax.php',
    //             type: 'post',

    //             data: {
    //                 action: "testing_funtion_to_test_all_code",

    //             },

    //             success: function(data) {

    //                 console.log("Test function done");


    //             }

    //         });

    //     });
    // });
</script>