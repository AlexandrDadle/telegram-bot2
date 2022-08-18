<?php

/* @var array $updates*/

?>
<?php //var_dump($updates);?>
<?php //var_dump($updates);?>

<?php foreach ($updates as $index => $update):?>
<?php echo $update->message->text;?><br>
<?php echo $update->updateId;?>

<br>
<?php //var_dump($update);?>
<?php endforeach;?>
