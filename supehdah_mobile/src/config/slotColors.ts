// Configurable colors for different slot states
export const slotColors = {
  available: '#4ade80', // green-400
  booked: '#ef4444',   // red-500
  past: '#9ca3af',     // gray-400
  closed: '#fbbf24'    // amber-400
};

// Style configurations for slot states
export const slotStyles = {
  available: {
    backgroundColor: slotColors.available,
    textColor: '#ffffff'
  },
  booked: {
    backgroundColor: slotColors.booked,
    textColor: '#ffffff'
  },
  past: {
    backgroundColor: slotColors.past,
    textColor: '#ffffff'
  },
  closed: {
    backgroundColor: slotColors.closed,
    textColor: '#ffffff'
  }
};