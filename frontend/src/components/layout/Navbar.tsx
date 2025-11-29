"use client";

import { LogOut, Menu } from "lucide-react";
import { useAuth } from "@/context/AuthContext";

export function Navbar({ onMenuClick }: { onMenuClick?: () => void }) {
  const { user, logout } = useAuth();

  return (
    <header className="flex items-center justify-between border-b border-white/5 bg-blue-900/60 px-4 py-3 text-sm text-white sticky top-0">
      <div className="flex items-center gap-4">
        <button
          onClick={onMenuClick}
          className="md:hidden p-2 rounded hover:bg-white/10"
        >
          <Menu size={20} />
        </button>
        <div>
          <p className="text-xs uppercase text-gray-400">Utente collegato</p>
          <p className="font-semibold">{user?.name ?? "--"}</p>
        </div>
      </div>
      <button
        onClick={logout}
        className="inline-flex items-center gap-2 rounded-full border border-white/10 px-3 py-1 text-xs uppercase tracking-wide text-white transition hover:bg-white/10"
      >
        <LogOut size={16} /> Logout
      </button>
    </header>
  );
}
