function generateNutrientColor(index) {
    const hue = (index * 15) % 360; // Adjust the hue increment to control the color gradient
    return `hsl(${hue}, 70%, 50%)`;
}