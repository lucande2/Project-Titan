$(document).ready(function() {
    $(".show-more").click(function(e) {
        e.preventDefault();
        console.log('Show more clicked'); // Debugging line
        $(this).parent().find(".hidden-meal").removeClass("hidden-meal");
        $(this).addClass("hidden");
        $(this).siblings(".show-less").removeClass("hidden");
    });

    $(".show-less").click(function(e) {
        e.preventDefault();
        console.log('Show less clicked'); // Debugging line
        let meals = $(this).parent().find(".meal");
        meals.slice(4).addClass("hidden-meal"); // Hide meals 5 and onwards
        $(this).addClass("hidden");
        $(this).siblings(".show-more").removeClass("hidden");
    });
});
