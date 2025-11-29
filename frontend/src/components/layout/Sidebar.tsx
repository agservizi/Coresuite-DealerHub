"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Building2, ChevronLeft, ChevronRight, FileText, Gauge, Shield, Upload } from "lucide-react";
import { useAuth } from "@/context/AuthContext";
import { useState } from "react";

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

export function Sidebar() {
  const pathname = usePathname();
  const { user } = useAuth();
  const [collapsed, setCollapsed] = useState(false);

  const links = [
    ...baseLinks,
    ...(user?.role === "AFFILIATO" ? affiliateLinks : []),
    ...(user?.role === "SUPERADMIN" ? adminLinks : []),
  ];

  return (
    <aside className={`hidden min-h-screen flex-col bg-slate-900/70 py-8 text-slate-200 md:flex sticky top-0 transition-all duration-300 ${
      collapsed ? "w-16 px-2" : "w-64 px-4"
    }`}>
      <div className="mb-8 flex items-center justify-between">
        {!collapsed && <div className="text-xl font-semibold">DealerHub</div>}
        <button
          onClick={() => setCollapsed(!collapsed)}
          className="rounded p-1 hover:bg-slate-800/60 transition"
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
              className={`flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition hover:bg-slate-800/60 ${
                active ? "bg-slate-800 text-white" : "text-slate-300"
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
  );
}
