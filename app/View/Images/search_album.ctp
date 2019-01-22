<?php
$this->extend('/Commons/master');
$this->start('extra');
?>
<script>
    var start_album = <?php echo $album_id; ?>;
    var start_page = <?php echo $page; ?>;
</script>
<?php $this->end(); ?>

