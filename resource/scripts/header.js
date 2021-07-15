const modal = document.getElementsByClassName("modal")[0];
const modal__content = document.getElementsByClassName("modal__content")[0];
const addWallet = document.getElementById("addWallet");

addWallet.addEventListener("click", ()=>{
   modal__content.innerHTML = `
      <center>
         <h2 style=" font-weight: 600; ">Add new wallet</h2><br>
      </center>

      <form action="/user_functions/addWallet" method="POST">
         <label>RSK wallet address:</label><br>
         <input type="text" name="wallet" title="EMV-compatible wallet address" pattern="0x([A-Fa-f0-9]{40})" required style="width: 100%; margin-top: 10px;"><br><br>
         <input class="btn" type="submit" name="add_wallet" value="Add wallet" style=" width: 100%; ">
      </form>
      
    `;
   modal.classList.remove("hidden-element");
   document.body.style = "overflow: hidden";
})



modal.addEventListener("click", (event)=>{
   modal.classList.add("hidden-element");
   document.body.style = "";
})

modal__content.addEventListener("click", (event)=>{
   event.stopPropagation();
   })


      

		// var lastScrollTop = 41;
  //           navbar = document.getElementById("header");
  //           logo_div = document.getElementById("logo");
  //           logo_img = logo_div.getElementsByTagName("img")[0];
  //           list_menu_array = document.getElementsByClassName("menu_link");
  //        window.addEventListener("scroll", function(){
  //           var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  //           if (scrollTop > lastScrollTop) {
  //              logo_div.style.margin = "13px 0px";
  //              navbar.style.height = "64px";
  //              logo_img.style.height = "38px";
  //              for (var i = 0; i < list_menu_array.length; i++) {
  //                 list_menu_array.item(i).style.padding="22px 24px";
  //              }
  //           } else {
  //              logo_div.style.margin = "19px 0px";
  //              navbar.style.height = "84px";
  //              logo_img.style.height = "45px";
  //              for (var i = 0; i < list_menu_array.length; i++) {
  //                 list_menu_array.item(i).style.padding="32px 24px";
  //              }
  //           }
  //        })




     
