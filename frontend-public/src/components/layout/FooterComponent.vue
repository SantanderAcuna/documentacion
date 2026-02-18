<script setup lang="ts">
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="govco-footer" role="contentinfo">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h3>Alcaldía Municipal</h3>
          <p>Gobierno Digital al servicio de la ciudadanía</p>
        </div>
        
        <div class="col-md-4">
          <h3>Enlaces de interés</h3>
          <ul class="footer-links">
            <li><a href="/transparencia">Transparencia</a></li>
            <li><a href="/pqrs">PQRS</a></li>
            <li><a href="#">Datos Abiertos</a></li>
          </ul>
        </div>
        
        <div class="col-md-4">
          <h3>Contacto</h3>
          <p>
            <i class="fas fa-phone" aria-hidden="true"></i>
            <span class="sr-only">Teléfono:</span>
            (601) 123-4567
          </p>
          <p>
            <i class="fas fa-envelope" aria-hidden="true"></i>
            <span class="sr-only">Correo:</span>
            contacto@alcaldia.gov.co
          </p>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; {{ currentYear }} Alcaldía Municipal. Todos los derechos reservados.</p>
        <p class="compliance-text">
          Cumplimiento normativo: Ley 1712/2014 - Ley 1581/2012 - WCAG 2.1 AA
        </p>
      </div>
    </div>
  </footer>
</template>

<style scoped lang="scss">
.govco-footer {
  background-color: $gris-oscuro;
  color: $blanco;
  padding: $spacing-xl 0 $spacing-md;
  margin-top: $spacing-xl;
  
  h3 {
    color: $blanco;
    font-size: 1.2rem;
    margin-bottom: $spacing-md;
  }
  
  p {
    margin-bottom: $spacing-sm;
  }
  
  i {
    margin-right: $spacing-xs;
  }
}

.footer-links {
  list-style: none;
  padding: 0;
  
  li {
    margin-bottom: $spacing-xs;
  }
  
  a {
    color: $blanco;
    text-decoration: none;
    
    &:hover {
      color: $amarillo-bandera;
      text-decoration: underline;
    }
  }
}

.footer-bottom {
  margin-top: $spacing-xl;
  padding-top: $spacing-md;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  text-align: center;
  
  p {
    margin: $spacing-xs 0;
    font-size: 0.9rem;
  }
  
  .compliance-text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
  }
}
</style>
