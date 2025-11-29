"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Building2, ChevronLeft, ChevronRight, FileText, Gauge, Shield, Upload } from "lucide-react";
import { useAuth } from "@/context/AuthContext";

const baseLinks = [{ label: "Dashboard", href: "/dashboard", icon: Gauge }];

const affiliateLinks = [
  { label: "I miei contratti", href: "/contracts", icon: FileText },
  { label: "Nuovo contratto", href: "/contracts/new", icon: Upload },
  { label: "Controllo copertura", href: "/coverage", icon: Shield },
];

const adminLinks = [
  { label: "Gestione contratti", href: "/contracts", icon: FileText },
  { label: "Affiliati", href: "/affiliates", icon: Building2 },
];

export function Sidebar({ isOpen, onClose, collapsed, onToggleCollapsed }: { isOpen?: boolean; onClose?: () => void; collapsed?: boolean; onToggleCollapsed?: (collapsed: boolean) => void }) {
  const pathname = usePathname();
  const { user } = useAuth();
  // const [collapsed, setCollapsed] = useState(false);

  const links = [
    ...baseLinks,
    ...(user?.role === "AFFILIATO" ? affiliateLinks : []),
    ...(user?.role === "SUPERADMIN" ? adminLinks : []),
  ];

  return (
    <>
      {isOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/50 md:hidden"
          onClick={onClose}
        />
      )}
      <aside className={`flex-col bg-blue-950 py-8 text-white fixed top-0 left-0 h-full transition-all duration-300 md:flex ${
        collapsed ? "w-16 px-2" : "w-64 px-4"
      } ${
        isOpen ? "fixed inset-y-0 left-0 z-50 w-64 px-4 md:relative md:inset-auto" : "hidden md:flex"
      }`}>
      <div className="mb-8 flex items-center justify-between">
        {!collapsed && <div className="text-xl font-semibold">DealerHub</div>}
        <button
          onClick={() => onToggleCollapsed?.(!collapsed)}
          className="rounded p-1 hover:bg-blue-800 transition"
        >
          {collapsed ? <ChevronRight size={18} /> : <ChevronLeft size={18} />}
        </button>
      </div>
      <nav className="flex flex-1 flex-col gap-2">
        {links.map(({ href, icon: Icon, label }) => {
          const active = pathname.startsWith(href);
          return (
            <Link
              key={href}
              href={href}
              className={`flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition hover:bg-blue-800 ${
                active ? "bg-blue-800 text-white" : "text-gray-300"
              } ${collapsed ? "justify-center px-2" : ""}`}
              title={collapsed ? label : undefined}
            >
              <Icon size={18} />
              {!collapsed && <span className="transition-opacity duration-300">{label}</span>}
            </Link>
          );
        })}
      </nav>
    </aside>
    </>
  );
}
