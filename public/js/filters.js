window.onload = () => {
    const FiltersForm = document.querySelector("#filters");
    //console.log('fmviuhiumvgb');

    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {
            //on interroge les clics
            const Form = new FormData(FiltersForm);

            // on fabrique la "querystring"
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value);
                //console.log(Params.toString());
            })

            //on recupere l'url active
            const Url = new URL(window.location.href);


            //on lance la requete ajax
            fetch(Url.pathname + "?" + Params.toString() + "&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(data => {
                const content = document.querySelector("#content");
                content.innerHTML = data.content;
            }).catch(e => alert(e));
        });
    });
}