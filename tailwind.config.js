/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.{php,html,js}",
    "./public/**/*.{php,html,js}",
    "./app/**/*.{php,html,js}"
  ],
  safelist: [
    // === LAYOUT BÁSICO ===
    'container', 'mx-auto', 'max-w-7xl', 'max-w-4xl', 'max-w-2xl',
    
    // Spacing común
    'px-4', 'px-6', 'px-8', 'py-4', 'py-8', 'py-12', 'py-16', 'py-20',
    'mb-2', 'mb-4', 'mb-6', 'mb-8', 'mb-10', 'mb-12',
    'mt-4', 'mt-8', 'space-x-4', 'space-y-4', 'gap-4', 'gap-6', 'gap-8', 'gap-12',
    
    // Layout & Flexbox
    'flex', 'inline-flex', 'grid', 'block', 'inline-block', 'hidden',
    'flex-col', 'flex-row', 'items-center', 'justify-center', 'text-center', 'text-left',
    'grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4',
    
    // === GRADIENTES Y COLORES ===
    'bg-gradient-to-r', 'bg-gradient-to-br', 'bg-gradient-to-bl', 'bg-gradient-to-l', 'bg-gradient-to-t', 'bg-gradient-to-b',
    
    // Colores específicos que usas
    'from-pink-600', 'via-purple-700', 'via-blue-600', 'via-green-500', 'to-yellow-500',
    'from-blue-600', 'to-purple-600', 'from-red-400', 'to-yellow-400',
    'from-orange-400', 'to-orange-600',
    
    // Colores de fondo y texto
    'bg-white', 'bg-black', 'bg-gray-100', 'bg-gray-800', 'bg-blue-50', 'bg-transparent',
    'text-white', 'text-gray-600', 'text-gray-700', 'text-gray-800', 'text-blue-600', 'text-orange-600',
    'text-yellow-300', 'text-blue-100', 'text-xl', 'text-2xl', 'text-3xl', 'text-4xl', 'text-5xl', 'text-6xl', 'text-8xl',
    
    // Borders
    'border', 'border-2', 'border-white', 'border-gray-200',
    
    // === ANIMACIONES ===
    'animate-pulse', 'animate-bounce', 'animate-spin', 'animate-ping',
    'animate-gradient-x', 'animate-wiggle', 'animate-rainbow',
    
    // Transiciones
    'transition-all', 'transition-colors', 'transition-transform', 'transition-shadow',
    'duration-200', 'duration-300', 'duration-500', 'ease-in-out',
    
    // === TYPOGRAPHY ===
    'font-thin', 'font-light', 'font-normal', 'font-medium', 'font-semibold', 'font-bold', 'font-extrabold', 'font-black',
    'text-xs', 'text-sm', 'text-base', 'text-lg', 'leading-relaxed',
    
    // === EFFECTS ===
    'shadow-sm', 'shadow', 'shadow-md', 'shadow-lg', 'shadow-xl', 'shadow-2xl',
    'rounded', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-2xl', 'rounded-full',
    'opacity-20', 'overflow-hidden', 'relative', 'absolute', 'inset-0', 'z-10',
    
    // === HOVER STATES ===
    'hover:bg-blue-50', 'hover:bg-white', 'hover:text-blue-600', 'hover:shadow-xl',
    'hover:scale-105', 'hover:scale-110',
    
    // === RESPONSIVE DESIGN ===
    // MD breakpoint
    'md:text-2xl', 'md:text-4xl', 'md:text-8xl', 'md:flex', 'md:grid-cols-2', 
    'md:space-y-0', 'md:space-x-4', 'md:justify-center',
    
    // LG breakpoint  
    'lg:text-xl',
    
    // === WIDTH & HEIGHT ===
    'w-32', 'w-full', 'h-2', 'h-32', 'h-screen',
    
    // === GRUPO EFFECTS ===
    'group', 'group-hover:shadow-xl',
    
    // === PROSE (si usas contenido largo) ===
    'prose', 'prose-lg',
    
    // Patrones para colores completos (más específico)
    {
      pattern: /^(from|via|to)-(slate|gray|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/,
    },
    {
      pattern: /^(bg|text|border)-(slate|gray|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/,
    },
  ],
  theme: {
    extend: {
      // Tus animaciones personalizadas que ya funcionan
      keyframes: {
        'gradient-x': {
          '0%, 100%': {
            'background-size': '200% 200%',
            'background-position': 'left center'
          },
          '50%': {
            'background-size': '200% 200%',
            'background-position': 'right center'
          },
        },
        wiggle: {
          '0%, 100%': { transform: 'rotate(-3deg)' },
          '50%': { transform: 'rotate(3deg)' },
        },
        rainbow: {
          '0%, 100%': { 
            background: 'linear-gradient(90deg, #ff0000, #ff8000, #ffff00, #80ff00, #00ff00, #00ff80, #00ffff, #0080ff, #0000ff, #8000ff, #ff00ff, #ff0080)'
          },
          '16%': { 
            background: 'linear-gradient(90deg, #ff8000, #ffff00, #80ff00, #00ff00, #00ff80, #00ffff, #0080ff, #0000ff, #8000ff, #ff00ff, #ff0080, #ff0000)'
          },
          '32%': { 
            background: 'linear-gradient(90deg, #ffff00, #80ff00, #00ff00, #00ff80, #00ffff, #0080ff, #0000ff, #8000ff, #ff00ff, #ff0080, #ff0000, #ff8000)'
          }
        }
      },
      animation: {
        'gradient-x': 'gradient-x 3s ease infinite',
        'wiggle': 'wiggle 1s ease-in-out infinite',
        'rainbow': 'rainbow 2s linear infinite',
      }
    },
  },
  plugins: [],
};