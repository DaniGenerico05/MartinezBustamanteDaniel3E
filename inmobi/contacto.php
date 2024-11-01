<?php include "includes/header.php" ?>
    <section>
        <h2>Contactanos</h2>
        <!-- Imagen-->
    </section>
    <section>
        <h2>Llena el formulario de conacto</h2>
        <form action="">
            <fieldset>
                <legend>Informacion personal</legend>
                <div>
                    <label>Nombre</label>
                    <input type="text" name="name" id="name" placeholder="Escribe tu nombre">
                </div>
                <div>
                    <label>Email</label>
                    <input type="text" name="email" id="email" placeholder="tu@gmail.com">
                </div>
                <div>
                    <label>Telefono</label>
                    <input type="text" name="phone" id="phone" placeholder="555-5555-555">
                </div>
                <div>
                    <label for="msg">Mensaje</label>
                    <template name="msg" id="msg" aria-placeholder="Escribe tu mensaje"></template>
                </div>
            </fieldset>
            <fieldset>
                <legend>Propiedad</legend>
                <div>
                    <label>Vende o compra</label>
                    <input type="select" name="voc" id="phone">
                </div>
                <div>
                    <label>Cantidad</label>
                    <input type="select" name="cantidad" id="antidad">
                </div>
            </fieldset>
            <fieldset>
                <legend>Contacto</legend>
                <div>
                    <label for="tel">Telefono</label>
                    <input type="radio" name="tel" id="tel">
                    <label for="email">Email</label>
                    <input type="radio" name="email" id="email">
                </div>
                <div>
                    <label for="date">Fecha</label>
                    <input type="date" name="date" id="date">
                    <label for="hour">Hora</label>
                    <input type="time" name="hour" id="date">
                </div>
            </fieldset>
            <div>
                <button>Contactar</button>
            </div>
        </form>
    </section>
<?php  include "includes/footers.php" ?>