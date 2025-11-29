"use client";

import { LogOut, Menu } from "lucide-react";
import { useAuth } from "@/context/AuthContext";
import clsx from "clsx";

export function Navbar({ onMenuClick, collapsed }: { onMenuClick?: () => void; collapsed?: boolean }) {
  const { user, logout } = useAuth();

  return (
    <header className={clsx("flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 sticky top-0 z-10", collapsed ? "md:ml-16" : "md:ml-64")}>
      <div className="flex items-center gap-4">
        <button
          onClick={onMenuClick}
          className="md:hidden p-2 rounded hover:bg-gray-100"
        >
          <Menu size={20} />
        </button>
        <div>
          <p className="text-xs uppercase text-gray-500">Utente collegato</p>
          <p className="font-semibold">{user?.name ?? "--"}</p>
        </div>
      </div>
      <button
        onClick={logout}
        className="inline-flex items-center gap-2 rounded-full border border-gray-300 px-3 py-1 text-xs uppercase tracking-wide text-gray-700 transition hover:bg-gray-100"
      >
        <LogOut size={16} /> Logout
      </button>
    </header>
  );
}
