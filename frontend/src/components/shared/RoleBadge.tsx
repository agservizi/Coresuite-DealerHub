import type { UserRole } from "@/types";

const roleCopy: Record<UserRole, string> = {
  SUPERADMIN: "Superadmin",
  AFFILIATO: "Affiliato",
};

export function RoleBadge({ role }: { role: UserRole }) {
  const tone = role === "SUPERADMIN" ? "bg-amber-500/20 text-amber-200" : "bg-blue-500/20 text-blue-200";

  return (
    <span className={`rounded-full px-3 py-1 text-xs font-semibold ${tone}`}>
      {roleCopy[role]}
    </span>
  );
}
