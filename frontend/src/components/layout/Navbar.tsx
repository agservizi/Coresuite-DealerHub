"use client";

import { LogOut } from "lucide-react";
import { useAuth } from "@/context/AuthContext";

export function Navbar() {
  const { user, logout } = useAuth();

  return (
    <header className="flex items-center justify-between border-b border-white/5 bg-slate-900/60 px-4 py-3 text-sm text-slate-50 sticky top-0">
      <div>
        <p className="text-xs uppercase text-slate-400">Utente collegato</p>
        <p className="font-semibold">{user?.name ?? "--"}</p>
      </div>
      <button
        onClick={logout}
        className="inline-flex items-center gap-2 rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-wide text-slate-200 transition hover:bg-white/10"
      >
        <LogOut size={16} /> Logout
      </button>
    </header>
  );
}
