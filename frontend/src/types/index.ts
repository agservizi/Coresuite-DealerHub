export type UserRole = "SUPERADMIN" | "AFFILIATO";

export interface UserProfile {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  affiliateId?: number;
  permissions?: string[];
  token?: string;
}

export type ContractStatus =
  | "NUOVO"
  | "IN_ELABORAZIONE"
  | "INVIATO"
  | "ACCETTATO"
  | "RIFIUTATO";

export type ServiceType = "MOBILE" | "FIBRA" | "LUCE" | "GAS";

export interface ContractSummary {
  id: number;
  customerName: string;
  affiliateName: string;
  serviceType: ServiceType;
  provider: string;
  status: ContractStatus;
  createdAt: string;
  updatedAt: string;
}

export interface ContractPayload {
  customerName: string;
  customerEmail: string;
  customerPhone: string;
  documentFront?: File | null;
  documentBack?: File | null;
  signedForm?: File | null;
  provider: string;
  serviceType: ServiceType;
  status: ContractStatus;
  notes?: string;
}

export interface CoverageRequest {
  operator: "FASTWEB" | "WINDTRE" | "ILIAD" | "FIBRA";
  address: string;
  city: string;
  zipCode: string;
}

export interface CoverageResponse {
  operator: CoverageRequest["operator"];
  available: boolean;
  technologies: string[];
  notes?: string;
}

export interface DashboardStats {
  totalContracts: number;
  pendingContracts: number;
  affiliatesEnabled: number;
  coverageChecks: number;
}

export interface ChartSeries {
  label: string;
  data: number[];
  color: string;
}
