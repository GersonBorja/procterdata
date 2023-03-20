window.onload = () => {
      (function () {
            var script = document.createElement('script'); script.src = "//cdn.jsdelivr.net/npm/eruda"; document.body.appendChild(script); script.onload = function () {
                  eruda.init()
            }
      })();

      function detalles() {
            let btn = document.querySelectorAll(".cardMas")
            btn.forEach((item) => {
                  item.addEventListener("click", (e) => {
                        e.preventDefault()
                        $(".confirmacion").fadeIn()
                        let interno = item.dataset.interno
                        let barra = item.dataset.barra
                        let descripcion = item.dataset.descripcion
                        let factor = item.dataset.factor
                        let precio = item.dataset.precio
                        if (item.dataset.barra.length === 13) {
                              JsBarcode("#ean", barra, {
                                    format: "ean13"
                              })
                        } else {
                              ean.src = 'load.jpg'
                        }


                        document.getElementById("interno").textContent = interno
                        document.getElementById("descripcionP").textContent = descripcion
                        document.getElementById("barra").textContent = barra
                        document.getElementById("factorEmpaque").textContent = factor
                        document.querySelector(".inputPrecio").value = precio
                  })
            })
      }
      function actualizar(url) {
            fetch(url)
            .then((res) => res.json())
            .then((data) => {
                  if (data.status === "empty") {
                        document.querySelector("#cintillosList").innerHTML = `<i class="fa-regular fa-face-sad-cry"></i> Esto esta vacio`
                        document.querySelector("#cintillosList").classList.add("center")
                  }
                  let fragment = document.createDocumentFragment()
                  let template = document.querySelector("#lista").content
                  data.forEach((item) => {
                        let share = `*${item["descripcion"]}*
                        *PRECIO:* $ ${item["precio"]}
                        *INTERNO:* ${item["interno"]}`
                        template.querySelector(".cardProducto .cardDescripcion").textContent = item["descripcion"]
                        template.querySelector(".cardPrecio").innerHTML = `<b>PRECIO: $ ${item["precio"]}</b>`
                        template.querySelector(".cardInterno").textContent = `INTERNO: ${item["interno"]}`
                        template.querySelector(".cardExistencia").textContent = `EXISTENCIA: ${item["existencia"]}`
                        template.querySelector(".cardMas").dataset.descripcion = item["descripcion"]
                        template.querySelector(".cardMas").dataset.barra = item["barra"]
                        template.querySelector(".cardMas").dataset.interno = item["interno"]
                        template.querySelector(".cardMas").dataset.factor = item["factorEmpaque"]
                        template.querySelector(".cardMas").dataset.precio = item["precio"]
                        template.querySelector(".cardCompartir").href = `whatsapp://send?text=${share}`

                        const clone = template.cloneNode(true)
                        fragment.appendChild(clone)

                  })
                  document.querySelector("#cintillosList").innerHTML = ''
                  document.querySelector("#cintillosList").appendChild(fragment)
                  detalles()
            })

      }


      let boton = document.querySelectorAll(".btnScaner")
      let cerrarScaner = document.querySelectorAll(".cerrarEscaner")
      cerrarScaner.forEach((item) => {
            item.addEventListener("click", (e) => {
                  e.preventDefault()
                  $(".confirmacion").fadeOut()
            })
      })




      const btnGuardar = document.getElementById("guardar")

      let form = document.getElementById('miform')
      form.addEventListener("submit",
            (event) => {
                  event.preventDefault()
                  let usr = obtenerUsuario()
                  let frm = new FormData(form)
                  frm.set("barra", barra.textContent)
                  frm.set("descripcion", descripcionP.textContent)
                  frm.set("interno", interno.textContent)
                  frm.set("autor", usr)

                  fetch('guardar.api.php', {
                        method: 'POST',
                        body: frm
                  })
                  .then((res) => res.json())
                  .then((data) => {
                        if (data.status == "OK") {
                              document.querySelector(".inputCintillo").value = ''
                              document.querySelector(".inputPrecio").value = ''
                              $(".confirmacion").fadeOut()
                              actualizarCantidad()
                        }
                        document.querySelector("#cintillosList").classList.remove("center")


                        alert(data.msg)
                  })
            })

      function stats (busqueda, autor) {
            let datos = new FormData()
            datos.set("busqueda", busqueda)
            datos.set("autor", autor)
            fetch('estadisticas.api.php', {
                  method: 'POST',
                  body: datos
            })
            .then((res) => res.json())
            .then((data) => {
                  console.log("saved!")
            })
      }

      buscador.addEventListener("submit", (e) => {
            e.preventDefault()
            if (buscadorInput.value.length > 0) {
                  let autor = obtenerUsuario()
                  let busquedas = buscadorInput.value
                  let busqueda = new FormData(buscador)
                  busqueda.set("categoria",
                        categoria.value)
                  fetch('buscador.api.php',
                        {
                              method: 'POST',
                              body: busqueda
                        })
                  .then((res) => res.json())
                  .then((data) => {
                        let fragment = document.createDocumentFragment()
                        let template = document.querySelector("#lista").content
                        data.forEach((item) => {
                              let share = `*${item["descripcion"]}*
                              *PRECIO:* $ ${item["precio"]}
                              *INTERNO:* ${item["interno"]}`
                              template.querySelector(".cardDescripcion").textContent = item["descripcion"]
                              template.querySelector(".cardPrecio").innerHTML = `<b>PRECIO: $ ${item["precio"]}</b>`
                              template.querySelector(".cardInterno").textContent = `INTERNO: ${item["interno"]}`
                        template.querySelector(".cardExistencia").textContent = `EXISTENCIA: ${item["existencia"]}`
                              template.querySelector(".cardMas").dataset.descripcion = item["descripcion"]
                              template.querySelector(".cardMas").dataset.barra = item["barra"]
                              template.querySelector(".cardMas").dataset.interno = item["interno"]
                              template.querySelector(".cardMas").dataset.factor = item["factorEmpaque"]
                              template.querySelector(".cardMas").dataset.precio = item["precio"]
                              template.querySelector(".cardCompartir").href = `whatsapp://send?text=${share}`

                              const clone = template.cloneNode(true)
                              fragment.appendChild(clone)

                        })

                        document.querySelector(".result").innerHTML = `Se encontraron <b>${data.length}</b> coincidencias de <b>"${busqueda.get("buscador")}"</b>`
                        document.querySelector("#cintillosList").innerHTML = ''
                        document.querySelector("#cintillosList").appendChild(fragment)
                        detalles()
                        stats(busquedas, autor)
                  })
            } else {
                  alert("Ingresa una palabra o código para iniciar tu búsqueda")
            }

      })

      $("select").change((e) => {
            if (e.target.value === "TODAS") {
                  document.querySelector(".result").innerHTML = `TODOS LOS PRODUCTOS`
                  actualizar('codigos.php')
            } else {
                  let datos = new FormData()
                  datos.set("categoria",
                        e.target.value)
                  fetch('categoria.api.php',
                        {
                              method: 'POST',
                              body: datos
                        })
                  .then((res) => res.json())
                  .then((data) => {
                        let fragment = document.createDocumentFragment()
                        let template = document.querySelector("#lista").content
                        data.forEach((item) => {
                              let share = `*${item["descripcion"]}*
                              *PRECIO:* $ ${item["precio"]}
                              *INTERNO:* ${item["interno"]}`
                              template.querySelector(".cardDescripcion").textContent = item["descripcion"]
                              template.querySelector(".cardPrecio").innerHTML = `<b>PRECIO: $ ${item["precio"]}</b>`
                              template.querySelector(".cardInterno").textContent = `INTERNO: ${item["interno"]}`
                        template.querySelector(".cardExistencia").textContent = `EXISTENCIA: ${item["existencia"]}`
                              template.querySelector(".cardMas").dataset.descripcion = item["descripcion"]
                              template.querySelector(".cardMas").dataset.barra = item["barra"]
                              template.querySelector(".cardMas").dataset.interno = item["interno"]
                              template.querySelector(".cardMas").dataset.factor = item["factorEmpaque"]
                              template.querySelector(".cardMas").dataset.precio = item["precio"]
                              template.querySelector(".cardCompartir").href = `whatsapp://send?text=${share}`

                              const clone = template.cloneNode(true)
                              fragment.appendChild(clone)

                        })

                        document.querySelector(".result").innerHTML = `<b>(${data.length})</b>Categoria "${datos.get("categoria")}"`
                        document.querySelector("#cintillosList").innerHTML = ''
                        document.querySelector("#cintillosList").appendChild(fragment)
                        detalles()
                  })

            }
      })




      actualizar('codigos.php')
      $(document).ready(function() {
            $('select').niceSelect();
      })

      function validarCookie() {
            let cookie = document.cookie
            let user = cookie.split("=")
            if (user.length > 1) {
                  document.querySelector(".modal").style.display = "none"
                  saludo.innerHTML = `<i class="fa-regular fa-hand-peace"></i> HOLA ${user[1]}!`
                  //btnGenerarCintillos.href = `editarExcel.php?autor=${user[1]}`


            }
      }
      iniciarApp.addEventListener("click",
            (e) => {
                  e.preventDefault()
                  if (nombre.value == "") {
                        alert("llena el campo nombre")
                  } else {
                        document.cookie = `user=${nombre.value.trim().toUpperCase()}; max-age=1728000`;
                        $(".modal").fadeOut();
                        validarCookie()
                        actualizarCantidad()

                  }
            })

      function obtenerUsuario() {
            let cookie = document.cookie
            let user = cookie.split("=")
            return user[1]
      }

      function obtenerCantidadCintillos() {
            let usr = obtenerUsuario()
            let datos = new FormData()
            datos.set("autor",
                  usr)
            fetch('cantidad_cintillos.php',
                  {
                        method: 'POST',
                        body: datos
                  })
            .then((res) => res.json())
            .then((data) => {
                  fecha(data)
                  btnDescargarCintillos.href = `editarExcel.php?autor=${usr}`
            })
      }


      function fecha (cant) {
            const fecha = new Date()

            let fechaArchivo = `${fecha.getDate()}${(fecha.getMonth()+1)}${fecha.getFullYear()}`
            file.textContent = `CINTILLOS_${fechaArchivo}_.xlsx`
            infoDescargas.innerHTML = `Llevas ${cant} cintillos agregados al documento`

      }

      btnGenerarCintillos.addEventListener("click",
            (e) => {
                  e.preventDefault()
                  obtenerCantidadCintillos()
                  document.querySelector(".modalDescargas").style.display = "flex"
            })
      document.querySelector(".cerrarDescargas").addEventListener("click",
            (e) => {
                  e.preventDefault()
                  $(".modalDescargas").fadeOut()
            })

      function actualizarCantidad() {
            let usr = obtenerUsuario()
            let datos = new FormData()
            datos.set("autor",
                  usr)
            fetch('cantidad_cintillos.php',
                  {
                        method: 'POST',
                        body: datos
                  })
            .then((res) => res.json())
            .then((data) => {
                  if (data > 0) {
                        btnGenerarCintillos.style.display = "block"
                        btnGenerarCintillos.innerHTML = `<b>${data}</b> <i class="fa-solid fa-receipt"></i>`
                  } else {
                        btnGenerarCintillos.style.display = "none"
                  }
            })
      }

      btnDescargarCintillos.addEventListener("click",
            () => {
                  document.querySelector(".descargando").style.display = "flex"
                  setTimeout(() => {
                        $(".descargando").fadeOut()
                        $(".modalDescargas").fadeOut()
                        actualizarCantidad()

                  }, 2000)
            })

      let btnLimpiarInput = document.querySelector(".limpiarInput")
      btnLimpiarInput.addEventListener("click",
            (e) => {
                  e.preventDefault()
                  buscador.reset()
            })
      validarCookie()
      actualizarCantidad()




}