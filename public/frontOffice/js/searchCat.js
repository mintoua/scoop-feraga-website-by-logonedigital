window.onload = ()=>{
    const FiltersForm = document.querySelector("#filters");

    //iterate on inputs
    document.querySelectorAll("#filters input").forEach(input =>{
        input.addEventListener("change",()=>{
            // get clics and thier data
            const Form = new FormData(FiltersForm);

            //create a "queryString
            const Params = new URLSearchParams();

            Form.forEach((value, key)=>{
                Params.append(key, value);
            })

            console.log(Params.toString());
            //get the active url
            const Url = new URL(window.location.href);

            //start the request
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1",{
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(
                response => response.json()
            ).then(data => {
                //get the content to replace
                const content = document.querySelector("#all");

                //replace
                content.innerHTML = data.content;

                history.pushState({}, null, Url.pathname + "?" + Params.toString())
            }).catch(e=>alert(e));
        })
    })
}