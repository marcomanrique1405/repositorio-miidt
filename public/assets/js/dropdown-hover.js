$(document).ready(function () {
  $(".dropdown")
    .on("mouseover", function () {
      $(this).find(".nav-link").dropdown("show");
    })
    .on("mouseout", function () {
      $(this).find(".nav-link").dropdown("hide");
    });
});
