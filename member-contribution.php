<?php
/**
 * Plugin Name:       Member Contribution
 * Plugin URI:        http://www.michaelcastrillo.com
 * Description:       Just Insert Data into Custom Form
 * Version:           1.0
 * Author:            Lucena Digital Space
 * Author URI:        http://www.michaelcastrillo.com
 */
function mrks_scripts() {
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//code.jquery.com/jquery-1.12.4.js');
}
function load_jquery_ui() {
	wp_enqueue_script('jqueryui', '//code.jquery.com/ui/1.12.1/jquery-ui.js');
	wp_enqueue_style('jqueryuistyle', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
}

add_action( 'wp_enqueue_scripts', 'mrks_scripts',10 );
add_action( 'wp_enqueue_scripts', 'load_jquery_ui',12 );

 if (isset($_POST['submit'])) {
	 $submitpayment = $_POST['submit'];
	 if (!empty($submitpayment)) {
		
		global $wpdb;
		$table_name ='wp_mcmuserpayment';
		$accountnumber = $_POST['accountnumber'];
		$monthlydues = $_POST['monthlydues'];
		$medicalpayment = $_POST['medicalpayment'];
		$damayangfe = $_POST['damayangfe'];
		$nextduedate = $_POST['nextduedate'];
		$totalmonthlypayment = $_POST['totalmonthlypayment'];
 
		$success = $wpdb->insert("wp_mcmuserpayment", array(
			"accountnumber" => $accountnumber,
			"monthlydues" => $monthlydues,
			"medicalpayment" => $medicalpayment,
			"damayangfe" => $damayangfe,
			"totalmonthlypayment" => $totalmonthlypayment,
			"nextduedate" => $nextduedate,
			"dateofpayment" => current_time('mysql'),
		));

		$success1 = $wpdb->update('wp_participants_database', array('next_due_date' => $nextduedate), array('account_number' => $accountnumber));

		if($success) {
			echo '<div class="alert-payment-success">Payment Success!</div>';
		} else {
			echo '<div class="alert-payment-failed">Account number not found</div>';
		}
}
else { 
	echo "Empty";
}
 }

function custom_payment_form() {

 ?>

<div class="form payment-form">
 <form action ="<?php echo $_SERVER['REQUEST_URI']; ?>" method ="post"> 
 <div class="form-row col-4"><label for name=""> Account Number: </label><input type="text" name="accountnumber" id ="account-number" placeholder="Enter account number" autocomplete="off"></div>
 <div class="form-row col-4"><label for name="MmonthlDues"> Monthly Dues: </label><input type="text" name="monthlydues" id ="monthly-dues" class="input monthlydues" autocomplete="off"></div>
 <div class="form-row col-4"><label for name="MedicalPayment"> Medical Payment: </label><input type="text" name="medicalpayment" id = "medical-payment" class="input medicalpayment" autocomplete="off"></div>
 <div class="form-row col-4"><label for name="DamayangFE"> Damayang FE: </label><input type="text" name="damayangfe" id = "damayang-fe" placeholder="" autocomplete="off"></div>
 <div class="form-row col-4"><label > Total Monthly Payment: </label><input type="text" name="totalmonthlypayment" id="total-monthly-payment" onblur="getTotal()" autocomplete="off"></div>
 <div class="form-row col-4"><label for name="nextduedate"> Due Date: </label><input type="text" name="nextduedate" id = "next-duedate" placeholder="Enter next due date" autocomplete="off"></div>
 </div>
 <input type ="text" name ="date-payment-encoded" id ="date-payment-encoded" hidden>
 <div class="form-row col-" style="clear:both;"><input type="submit" name="submit" value="Add payment" onclick="return callValidation();this.disabled=true; this.value='Sending…';"></div>
 <script>
function getTotal() {
	    var nextduedate = document.getElementsByName('nextduedate')[0].value;
        var monthlydues = document.getElementsByName('monthlydues')[0].value;
        var medicalpayment = document.getElementsByName('medicalpayment')[0].value;
        var damayangfe = document.getElementsByName('damayangfe')[0].value;
        var totalmonthlypayment = (+monthlydues) + (+medicalpayment) + (+damayangfe);
        document.getElementsByName('totalmonthlypayment')[0].value = totalmonthlypayment;
    }
function callValidation(){
        if(document.getElementById('account-number').value == ''){
            alert('Please add account number.');
            return false;
        }
        if(document.getElementById('monthly-dues').value == ''){
            alert('Please add monthly dues..');
            return false;
        }
        if(document.getElementById('medical-payment').value == ''){
            alert('Please add medical payment.');
            return false;
        }
        if(document.getElementById('damayang-fe').value == ''){
            alert('Please add Damayang Far East payment.');
            return false;
        }
		if(document.getElementById('next-duedate').value == ''){
            alert('Please add Next Due Date.');
            return false;
        }

        return true;
    }
</script>

 </form></div>
 




 <?php
}
add_shortcode('display_form', 'custom_payment_form');




function display_custom_form() {
    global $wpdb;
    ?>

    <table style="margin-top:30px; border-top:solid 2px #111;">
    <tr class="payment-history-table">
    <th>Account Number</td>
    <th>Monthly Dues</td>
    <th>Medical Payment</td>
    <th>Damayang FE</td>
    <th>Total Monthly Payment</td>
    <th>Date of Payment</td>
	    <th>Next Due Date</td>
    </tr>
            <?php
    $result = $wpdb->get_results ( 'SELECT * FROM wp_mcmuserpayment ORDER BY dateofpayment desc' );
    foreach ( $result as $return )   {
    ?>
    <tr class="payment-history-table">
    <?php echo "<tr><td>".$return->accountnumber."</td>" ?>
    <?php echo "<td>".$return->monthlydues."</td>"?>
    <?php echo "<td>".$return->medicalpayment."</td>"?>
    <?php echo "<td>".$return->damayangfe."</td>"?>
    <?php echo "<td>".$return->totalmonthlypayment."</td>"?>
    <?php echo "<td>".$return->dateofpayment."</td>"?>
	<?php echo "<td>".$return->nextduedate."</td></tr>"?>


        <?php } ?>
        </table>
        <?php 
    
}
add_shortcode('display_payment_history', 'display_custom_form');
?>
