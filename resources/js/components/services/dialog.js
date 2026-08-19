import Swal from 'sweetalert2'

/**
 * Exibe um diálogo de confirmação.
 * @param {string} title - Título do diálogo.
 * @param {string} text - Texto descritivo.
 * @param {string} icon - 'warning', 'error', 'success', 'info', 'question'.
 * @param {string} confirmButtonText - Texto do botão de confirmação.
 * @param {string} cancelButtonText - Texto do botão de cancelamento.
 * @returns {Promise<boolean>} - Retorna true se confirmado, false se cancelado.
 */
export async function confirm(
    title = 'Tem certeza?',
    text = 'Essa ação não poderá ser desfeita.',
    icon = 'warning',
    confirmButtonText = 'Sim, confirmar',
    cancelButtonText = 'Cancelar'
) {
    const result = await Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText,
        cancelButtonText,
    })
    return result.isConfirmed
}

/**
 * Exibe um alerta simples (apenas OK).
 * @param {string} title - Título.
 * @param {string} text - Texto.
 * @param {string} icon - 'success', 'error', 'info', 'warning'.
 */
export function alert(
    title = 'Aviso',
    text = '',
    icon = 'info'
) {
    return Swal.fire({
        title,
        text,
        icon,
        confirmButtonText: 'OK',
    })
}

export function toast(title, icon = 'success', timer = 3000) {
    return Swal.fire({
        title,
        icon,
        timer,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timerProgressBar: true,
    })
}