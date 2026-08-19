import { useToast } from 'vue-toastification'

let toastInstance = null

export function initToast() { }

export function getToast() {
    if (!toastInstance) {
        toastInstance = useToast()
    }
    return toastInstance
}

export function notifySuccess(message) {
    getToast().success(message)
}

export function notifyError(message) {
    getToast().error(message)
}

export function notifyInfo(message) {
    getToast().info(message)
}

export function notifyWarning(message) {
    getToast().warning(message)
}