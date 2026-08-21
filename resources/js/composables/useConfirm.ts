import { reactive } from 'vue';

type ConfirmVariant = 'default' | 'destructive';

interface ConfirmOptions {
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: ConfirmVariant;
}

interface ConfirmState extends Required<ConfirmOptions> {
    open: boolean;
    resolve: ((value: boolean) => void) | null;
}

export const confirmState: ConfirmState = reactive({
    open: false,
    title: '¿Estás seguro?',
    description: '',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    variant: 'default',
    resolve: null,
});

export function confirmDialog(options: ConfirmOptions = {}): Promise<boolean> {
    confirmState.title = options.title ?? '¿Estás seguro?';
    confirmState.description = options.description ?? '';
    confirmState.confirmText = options.confirmText ?? 'Confirmar';
    confirmState.cancelText = options.cancelText ?? 'Cancelar';
    confirmState.variant = options.variant ?? 'default';
    confirmState.open = true;

    return new Promise((resolve) => {
        confirmState.resolve = resolve;
    });
}

export function resolveConfirm(value: boolean) {
    confirmState.open = false;
    confirmState.resolve?.(value);
    confirmState.resolve = null;
}
