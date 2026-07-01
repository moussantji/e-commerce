import { CURRENCY } from "./config";

export function formatPrice(value) {
    const n = Number(value) || 0;
    return n.toLocaleString("fr-FR") + " " + CURRENCY;
}
