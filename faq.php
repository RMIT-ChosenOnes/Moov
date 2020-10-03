<!DOCTYPE html>
<html>

<head>
    <title>Moov</title>

    <!-- meta tag -->
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Chosen Ones">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-171692999-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-171692999-2');
    </script>

    <!-- JavaScript from Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <!-- CSS from Bootstrap v4.5.2 -->
    <link rel="stylesheet" type="text/css" href="/moov/assets/style/bootstrap.css">

    <!-- Self Defined CSS -->
    <link rel="stylesheet" type="text/css" href="/moov/assets/style/style.css?<?php echo date('l jS \of F Y h:i:s A'); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="/moov/assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/moov/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/moov/assets/favicon/favicon-16x16.png">
</head>

<body>
    <?php include 'header.php'; ?>

    <section class="accordion-section clearfix mt-3" aria-label="Question Accordions">
        <div class="container">

            <h2>Frequently Asked Questions </h2>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading p-3 mb-3" role="tab" id="heading0">
                        <h3 class="panel-title">
                            <a class="collapsed" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                What are the benefits of Solodev CMS?
                            </a>
                        </h3>
                    </div>
                    <div id="collapse0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading0">
                        <div class="panel-body px-3 mb-4">
                            <p>With Solodev CMS, you and your visitors will benefit from a finely-tuned technology stack that drives the highest levels of site performance, speed and engagement - and contributes more to your bottom line. Our users fell in love with:</p>
                            <ul>
                                <li>Light speed deployment on the most secure and stable cloud infrastructure available on the market.</li>
                                <li>Scalability – pay for what you need today and add-on options as you grow.</li>
                                <li>All of the bells and whistles of other enterprise CMS options but without the design limitations - this CMS simply lets you realize your creative visions.</li>
                                <li>Amazing support backed by a team of Solodev pros – here when you need them.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading p-3 mb-3" role="tab" id="heading1">
                        <h3 class="panel-title">
                            <a class="collapsed" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                How easy is it to build a website with Solodev CMS?
                            </a>
                        </h3>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
                        <div class="panel-body px-3 mb-4">
                            <p>Building a website is extremely easy. With a working knowledge of HTML and CSS you will be able to have a site up and running in no time.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading p-3 mb-3" role="tab" id="heading2">
                        <h3 class="panel-title">
                            <a class="collapsed" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                What is the uptime for Solodev CMS?
                            </a>
                        </h3>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
                        <div class="panel-body px-3 mb-4">
                            <p>Using Amazon AWS technology which is an industry leader for reliability you will be able to experience an uptime in the vicinity of 99.95%.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading p-3 mb-3" role="tab" id="heading3">
                        <h3 class="panel-title">
                            <a class="collapsed" role="button" title="" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                Can Solodev CMS handle multiple websites for my company?
                            </a>
                        </h3>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
                        <div class="panel-body px-3 mb-4">
                            <p>Yes, Solodev CMS is built to handle the needs of any size company. With our Multi-Site Management, you will be able to easily manage all of your websites.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>