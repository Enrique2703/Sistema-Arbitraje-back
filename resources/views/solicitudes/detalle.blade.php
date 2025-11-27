<!-- Modal Detalle Solicitud -->
<div id="detalleSolicitudModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-[420px] p-8 relative">
        <h2 class="text-xl font-bold mb-4">Datos de Solicitud</h2>
        <div class="mb-4">
            <h3 class="font-semibold text-gray-700 mb-2">Información del caso</h3>
            <div class="flex justify-between mb-1">
                <span class="text-gray-600">Estado:</span>
                <span id="detalleEstado" class="font-medium"></span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="text-gray-600">Fecha de Inicio:</span>
                <span id="detalleFechaInicio" class="font-medium"></span>
            </div>
        </div>
        <div class="mb-4">
            <h3 class="font-semibold text-gray-700 mb-2">Participes</h3>
            <div class="flex justify-between items-center border-b border-gray-200 pb-1 mb-1">
                <span id="detalleDemandante" class="text-gray-800"></span>
                <span class="text-xs text-gray-500">Demandante</span>
            </div>
            <div class="flex justify-between items-center border-b border-gray-200 pb-1 mb-1">
                <span id="detalleDemandado" class="text-gray-800"></span>
                <span class="text-xs text-gray-500">Demandado</span>
            </div>
        </div>
        <div class="mb-4">
            <h3 class="font-semibold text-gray-700 mb-2">Documentos</h3>
            <ul id="detalleDocumentos" class="text-gray-700 text-sm">
                <!-- Documentos se llenan por JS -->
            </ul>
        </div>
        <div class="flex justify-between mt-6">
            <button id="btnRechazarSolicitud" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Rechazar</button>
            <button id="btnAceptarSolicitud" class="px-4 py-2 bg-[#0a1128] text-white rounded hover:bg-[#1a2238]">Aceptar</button>
            <button id="btnCerrarDetalleSolicitud" class="px-4 py-2 border border-gray-400 rounded hover:bg-gray-100">Cerrar</button>
        </div>
    </div>
</div>
