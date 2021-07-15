// //Hide msg block
// function hide_msgbox(){
// 	document.getElementsByClassName("message-box")[0].style.opacity="0";
// 	setTimeout(function(){
// 		document.getElementsByClassName("message-box")[0].style.display="none";
// 	}, 850)
// }


notifs = document.querySelectorAll(".notif")
notifs.forEach((item)=>{
	item.addEventListener("click", ()=>{
		// item.classList.add("move-notif");
		if(!item.classList.contains("hide-notif") ){
			item.classList.add("hide-notif");
			item.classList.remove("active-notif");
			notifCount = document.querySelectorAll(".active-notif");
		}
		setTimeout(()=>{
			item.classList.add("hidden-element");
		}, 300)

		console.log(notifCount);
		console.log("ID:" + item.getAttribute("data-id"))
	})
	item.style="";
	
})