<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main-banner mbot15">
            <div class="banner-inner text-center">
                BANNER
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php echo $this->content()->renderWidget('zulu.tutorial-video') ?>
        </div>
        <div class="col-md-4">
            <?php echo $this->content()->renderWidget('announcement.list-announcements') ?>
        </div>
        <div class="col-md-4">
            <?php echo $this->content()->renderWidget('sitepage.tagcloud-sitepage') ?>
            <?php echo $this->content()->renderWidget('zulu.tagcloud') ?>
        </div>
    </div>
</div>