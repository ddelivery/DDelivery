<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Widget-2-2</title>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?=$staticURL?>css/screen.css"/>
    <link rel="stylesheet" href="<?=$staticURL?>css/jquery.mCustomScrollbar.min.css"/>
    <script type="text/javascript" src="<?=$staticURL?>jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>start-scroll.js"></script>
    <script type="text/javascript" src="<?=$staticURL?>widgetserver.js"></script>
</head>
<body>
<div id="content">
    <img id="<?=$staticURL?>img/ajax_loader.gif"/>
</div>
<script type="text/javascript">
var action_start = "<?=$actionStart?>";
WidgetServer.init("<?=$scriptURL?>", action_start);
</script>
</body>
</html>