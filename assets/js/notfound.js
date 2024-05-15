const $menu_mobile = document.querySelector("#menu_mobile");

$menu_mobile.addEventListener("click", function(){
    let $menu = document.querySelector("#menu");

    if(this.classList.contains("open")){
        $menu.style.height = "0px";
    }else{
        $menu.style.height = $menu.scrollHeight + "px";
    }
    this.classList.toggle("open");
});