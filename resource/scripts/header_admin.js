		var lastScrollTop = 41;
      navbar = document.getElementById("header");
      list_menu_array = document.getElementsByClassName("menu_link");
      
      var array_title = document.querySelectorAll('.title_list_item');
         window.addEventListener("scroll", function(){
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop) {
               array_title.forEach(
                  function(element, index, array) {
                     element.style = "display:none;";
                  }
               );
               for (var i = 0; i < list_menu_array.length; i++) {
                  list_menu_array.item(i).style.padding="22px 24px";
               }
            } else {
               navbar.style.height = "auto";
               array_title.forEach(
                  function(element, index, array) {
                     element.style = "display:inline";
                  }
               );
               for (var i = 0; i < list_menu_array.length; i++) {
                  list_menu_array.item(i).style.padding="32px 24px";
               }
            }
         })