//Nota n° 1: user-guide 
$(document).ready(function () {
    $("#logo").on("mouseover", function () {
        console.log("passou o mouse no logotipo!");
    });

    //Nota n° 2: user-guide -> test
    $("#logo").on("mouseover", function () {
        $("#banner h1").addClass("efeito");
    }).on("mouseout", function () {
        $("#banner h1").removeClass("efeito");
    });

    //Nota n° 3: user-guide -> tests
    $("#input-search").on("focus", function () {
        $("li.search").addClass("ativo");
    }).on("blur", function () {
        $("li.search").removeClass("ativo");
    });

    $(".thumbnails").owlCarousel({
        loop: true,
        margin: 10,
        //nav: true,
        //navText: ["Anterior", "Próximo"],
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 3
            },
            768: {
                items: 4
            }
        }
    });
    
    var owl = $(".thumbnails").data('owlCarousel');
    
    $("#btn-news-prev").on("click", function(){
        owl.prev();
    });
    
    $("#btn-news-next").on("click", function(){
        owl.next();
    });
    
    $("#page-up").on("click", function (event){
        $("body").animate({
            scrollTop: 0
        }, 1000);
        //Cancela o evento padrão da tag 'a' qaundo clicada
        event.preventDefault();
    });
    
    $("#btn-bars").on("click", function(){
        $("header").toggleClass("open-menu");
    });
    
    $("#menu-mobile-mask, .btn-close").on("click", function(){
        $("header").removeClass("open-menu");
    });
    
    $("#btn-search").on("click", function(){
        $("header").toggleClass("open-search");
        $("#input-search-mobile").focus();
    });
});


