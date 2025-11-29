import Link from "next/link";

const links = [
  { title: "FASTWEB", href: "/coverage?operator=FASTWEB" },
  { title: "WINDTRE", href: "/coverage?operator=WINDTRE" },
  { title: "ILIAD", href: "/coverage?operator=ILIAD" },
  { title: "Fibra Nazionale", href: "/coverage?operator=FIBRA" },
];

export function CoverageLinks() {
  return (
    <div className="glass-card p-4">
      <p className="mb-3 text-sm font-semibold text-gray-900">Controllo copertura</p>
      <div className="flex flex-col gap-3">
        {links.map((link) => (
          <Link
            key={link.title}
            href={link.href}
            className="rounded-lg border border-gray-300 px-3 py-2 text-sm text-emerald-600 transition hover:border-emerald-400/50"
          >
            {link.title}
          </Link>
        ))}
      </div>
    </div>
  );
}
