export const API_BASE_URL =
  process.env.NEXT_PUBLIC_API_BASE_URL ?? "https://dealer.coresuite.it/api";

export const MAX_UPLOAD_SIZE = 10 * 1024 * 1024; // 10 MB

export const PROVIDERS = ["FASTWEB", "WINDTRE", "ILIAD", "VODAFONE", "ENEL"];

export const SERVICE_TYPES = ["MOBILE", "FIBRA", "LUCE", "GAS"] as const;

export const CONTRACT_STATUSES = [
  "NUOVO",
  "IN_ELABORAZIONE",
  "INVIATO",
  "ACCETTATO",
  "RIFIUTATO",
] as const;
