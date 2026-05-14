@extends('layouts.site', ['title' => 'Lexi | Carrito', 'description' => 'Carrito de compra de Lexi.', 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space" data-cart-page>
  <h1 class="mb-4">Carrito</h1>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Subtotal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="cartItems"></tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total</th>
          <th id="cartTotal">0 EUR</th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <button id="clearCart" class="btn btn-outline-danger" type="button">Vaciar carrito</button>
</main>

<div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="h5 mb-0">Confirmar eliminacion</h2>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">Quieres eliminar este producto del carrito?</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="confirmRemoveBtn" type="button">Eliminar</button>
      </div>
    </div>
  </div>
</div>
    @endsection