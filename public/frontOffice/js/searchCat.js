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

            //get the active url
            const Url = new URL(window.location.href);
            console.log(Url);
        })
    })
}