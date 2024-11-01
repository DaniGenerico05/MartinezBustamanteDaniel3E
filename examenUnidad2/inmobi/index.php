<?php include "includes/header.php" ;

      require "includes/config/conn.php";
    connect();
    

?>  <br>
    <button type="button" > <a href="createSeller.php">crear vendedor</a> </button>
    <button type="button" >   <a href="propierties.php">crear propiedad</a> </button>
    <section>
        <h2>Mas sobre nosotros</h2>
        <div>
            <!-- Icono de seguridad Tabler Icon Amarillo-->
            <svg  xmlns="http://www.w3.org/2000/svg"  width="150px"  height=auto  viewBox="0 0 24 24"  fill="none"  stroke="#eee311"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
            <h3>Seguridad</h3>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero vitae praesentium eaque inventore harum, </p>
        </div>
        <div>
            <!-- Icono de precio Tabler Icon Amarillo-->
            <svg  xmlns="http://www.w3.org/2000/svg"  width="150"  height="auto"  viewBox="0 0 24 24"  fill="none"  stroke="#eee311"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-currency-dollar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
            <h3>Precio</h3>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero vitae praesentium eaque inventore harum, </p>
        </div>
        <div>
            <!-- Icono de tiempo Tabler Icon Amarillo-->
            <svg  xmlns="http://www.w3.org/2000/svg"  width="150px"  height="150px"  viewBox="0 0 24 24"  fill="none"  stroke="#eee311"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
            <h3>A tiempo</h3>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Libero vitae praesentium eaque inventore harum, </p>
        </div>
    </section>
    <main>
        <section>
            <h3>Casas y departamentos en ventas</h3>
            <div>
                <h4>Title anuncio 1</h4>
                <a href=""></a>
                <p></p>
                1000000.00
                <!-- Tres iconos baño,cochera,cama un div para cada icono-->
                 <div></div>
                 <div></div>
                 <div></div>
            </div>
            <div>
                <h4>Title anuncio 2</h4>
                <a href=""></a>
                <p></p>
                1999.00
                <!-- Tres iconos baño,cochera,cama un div para cada icono-->
                 <div></div>
                 <div></div>
                 <div></div>
            </div>
            <div>
                <h4>Title anuncio 3</h4>
                <a href=""></a>
                <p></p>
                2999.00
                <!-- Tres iconos baño,cochera,cama un div para cada icono-->
                 <div></div>
                 <div></div>
                 <div></div>
            </div>
            <a href="">Ver mas</a>
        </section>
    </main>
    <section>
        <h3>Encuentra la casa de tus Dreams</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
        <a href="">Contactanos</a>
    </section>
    <section>
        <div>
            <h1>Nuestro blog</h1>
            <div>Entrada blog 1</div>
            <div>Entrada blog 2</div>
        </div>
        <div>
            <h3>Testimonios</h3>
            <p>
                Lorem ipsum dolor, sit amet consectetur adipisicing elit.
            </p>
        </div>
    </section>
<?php  include "includes/footers.php" ?>
