<?php
// Here we'll set where the contact form is going to be mailed to. We can add multiple recipients if we want to by comma separating
$to_emails = [
 "stan.suee@gmail.com"
];

//We need to make sure the referrer is set, and that the domain the request is coming from is the same as this PHP script.
//Since this is a fairly open-ended script we need to prevent any scripts coming from unauthorized websites from hijacking it
if (isset($_SERVER['HTTP_REFERER'])) {
  //extract only the domain from the referrer url
  $referer_domain = parse_url($_SERVER['HTTP_REFERER']);
  $referer_domain = $referer_domain['host'];
  // get the domain that this script is running on
  $this_domain = $_SERVER['HTTP_HOST'];

  // only proceed to process the form if the domain's match
  if ($this_domain == $referer_domain) {
    
    // All contact form requests should include a $_POST['trap'] field as a honeypot. Since spam bots will often fill out ALL fields on a form, only process the request if this one is blank. You should hide this field on the front end with CSS so humans can't see it.
    if ($_POST['trap'] == "") {
      //message appears to be valid and not spam, so we'll send it through
      $subject = "Contact Form from ".$referer_domain;
      $reply_email = "no-reply@".$referer_domain;

       // set email headers
      $headers = "From: " . $reply_email . "\r\n";
      $headers .= "Reply-To: ".$reply_email . "\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
      
      // start building our HTML email message
      $message = "<html>  
          <body> 
            <h1 style='color:#004c99;margin:0;padding:0;'>$subject</h1> 
            <table>";

      // This is where we loop through all the POST fields that were sent to the script, and build them out into an HTML table
      foreach ($_POST as $field => $value) {
        if ($field != "trap") {
          //convert field key to label string by replacing underscores with spaces and title casing it for a prettier display
          $label = ucwords(str_replace("_", " ", $field));
          $message.= "
          <tr>
            <th>$label</th>
            <td>$value</td>
          </tr>";
        }
      }
      
      // Finish off our message
      $message .= "
        </table> 
      </body></html>";

      // Finally email the messages out to every email we have listed as a recipient! (defined at the top of the script)
      foreach ($to_emails as $recipient) {
        mail($recipient, $subject, $message, $headers);
      }
      
      // Success message
      echo "<p>Your message has been sent!</p>";
    }
  }
}