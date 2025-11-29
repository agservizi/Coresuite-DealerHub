import { API_BASE_URL } from "@/lib/constants";
import type {
  ContractPayload,
  ContractSummary,
  CoverageRequest,
  CoverageResponse,
  DashboardStats,
  UserProfile,
} from "@/types";

interface ApiRequestOptions extends RequestInit {
  token?: string;
  isFormData?: boolean;
}

const defaultHeaders = {
  "Content-Type": "application/json",
};

async function apiRequest<T>(
  endpoint: string,
  { token, isFormData, headers, ...options }: ApiRequestOptions = {}
): Promise<T> {
  const finalHeaders: Record<string, string> = {
    ...(isFormData ? {} : defaultHeaders),
    ...((headers as Record<string, string>) ?? {}),
  };

  if (token) {
    finalHeaders["Authorization"] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
    headers: finalHeaders,
    ...options,
  });

  if (!response.ok) {
    const errorPayload = await response.json().catch(() => ({}));
    throw new Error(errorPayload?.message ?? "Errore durante la chiamata API");
  }

  return response.json();
}

export function loginRequest(email: string, password: string) {
  return apiRequest<{ token: string; user: UserProfile }>("auth.php", {
    method: "POST",
    body: JSON.stringify({ action: "login", email, password }),
  });
}

export function logoutRequest(token: string) {
  return apiRequest<{ success: boolean }>("auth.php", {
    method: "POST",
    body: JSON.stringify({ action: "logout" }),
    token,
  });
}

export function meRequest(token: string) {
  return apiRequest<{ user: UserProfile }>("me.php", {
    token,
  });
}

export function fetchDashboardStats(token: string) {
  return apiRequest<DashboardStats>("stats.php", { token });
}

export function fetchContracts(token: string) {
  return apiRequest<ContractSummary[]>("contracts.php", { token });
}

export function createContract(token: string, data: ContractPayload) {
  const formData = new FormData();
  Object.entries(data).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      formData.append(key, value as string | Blob);
    }
  });
  return apiRequest<{ message: string }>("contracts.php", {
    method: "POST",
    body: formData,
    token,
    isFormData: true,
  });
}

export function fetchCoverage(token: string, payload: CoverageRequest) {
  return apiRequest<CoverageResponse>("coverage.php", {
    method: "POST",
    body: JSON.stringify(payload),
    token,
  });
}

export function uploadDocument(token: string, file: File, contractId: number) {
  const formData = new FormData();
  formData.append("file", file);
  formData.append("contractId", String(contractId));

  return apiRequest<{ path: string }>("upload.php", {
    method: "POST",
    body: formData,
    token,
    isFormData: true,
  });
}

export function fetchAffiliates(token: string) {
  return apiRequest("users.php", { token });
}

export function manageAffiliate(
  token: string,
  payload: Record<string, unknown>
) {
  return apiRequest("users.php", {
    method: "POST",
    body: JSON.stringify(payload),
    token,
  });
}
