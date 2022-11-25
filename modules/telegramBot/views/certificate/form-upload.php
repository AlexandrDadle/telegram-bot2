<?php
/** @var $token */
?>
<form action="https://api.telegram.org/bot<?php echo $token; ?>/setwebhook" enctype="multipart/form-data" >
    <label class="label">Enter your URL</label>
    <p class="control">
        <input class="input" type="text" name="url" />
    </p>
    <br/>
    <label class="label">Enter your Certificate</label>
    <p class="control">
        <input type="file" name="certificate" id="fileToUpload"/>
    </p>
    <br/>
    <div class="control is-grouped">
        <p class="control">
            <button class="button is-primary" name="submit">Set Webhook</button>
        </p>
    </div>


</form>