<?php include_once 'header.php'; ?>

<link rel="stylesheet" href="assets/lib/plyr/plyr.css">

<!-- Sessões -->
<section>
    <div id="call-to-action">
        <div class="container">
            <div class="row text-center">
                <h2> Videos </h2>
                <hr>
            </div>

            <div class="row">
                <div class="player">
                    <video src="uploads/videos/highlights.mp4" poster="uploads/videos/highlights.jpg" controls>
                        <track kind="captions" label="Português (Brasil)" src="uploads/legendas/legendas.vtt" 
                               srclang="pt-br" default>
                    </video>
                </div>
                <input id="volume" type="range" min="0" max="1" step="0.01" value="1">
                <button id="btn-play-pause" type="button" class="btn btn-success"> PLAY </button>
            </div>
        </div>

        <div id="news" class="container" style="top: 0">
            <div class="row text-center">
                <h2> Latest News </h2>
                <hr>
            </div>

            <div class="row thumbnails owl-carousel owl-theme">
                <div class="item" data-video-url="highlights">
                    <div class="item-inner">
                        <img src="uploads/videos/highlights.jpg" alt="Imagem do Vídeo">
                        <h3> Highlights </h3>
                    </div>
                </div>

                <div class="item" data-video-url="Orlando_City_Foundation_2015">
                    <div class="item-inner">
                        <img src="uploads/videos/Orlando_City_Foundation_2015.jpg" alt="Imagem do Vídeo">
                        <h3> Orlando City Foundation 2015 </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- FIM das Sessões -->

<?php 
    include_once 'footer.php';
    include_once 'scripts.php';
?>

<script src="assets/lib/plyr/plyr.js"></script>
<script>
    //Nota n° 5: user-guide 
    $(function () {
        $(".thumbnails .item").on("click", function () {
            $("video").attr({
                "src": "uploads/videos/" + $(this).data('video-url') + ".mp4",
                "poster": "uploads/videos/" + $(this).data('video-url') + ".jpg"
            });
        });

        //Nota n° 6: user-guide
        $("#volume").on("mousemove", function () {
            $('video')[0].volume = parseFloat($(this).val());
        });

        //Nota n° 8: user-guide
        $("#btn-play-pause").on("click", function () {
            var video = $("video")[0];

            if ($(this).hasClass('btn-success')) {
                $(this).text("STOP");
                video.play();
            } else {
                $(this).text("PLAY");
                video.pause();
            }

            $(this).toggleClass("btn-success btn-danger");
        });

        plyr.setup();
    });
</script>
