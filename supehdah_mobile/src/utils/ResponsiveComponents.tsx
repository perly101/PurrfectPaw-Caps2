import React from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  TextInputProps,
  TouchableOpacityProps,
  ViewProps,
  TextProps,
  ViewStyle,
  TextStyle,
  ImageBackground,
  ImageBackgroundProps,
  ScrollView,
  ScrollViewProps,
  KeyboardAvoidingView,
  Platform,
  KeyboardAvoidingViewProps,
} from 'react-native';
import { useTheme } from './theme';
import { normalizeFont, normalize, horizontalScale, verticalScale } from './responsive';

// ========= Container Component =========
interface ContainerProps extends ViewProps {
  center?: boolean;
  padding?: boolean;
  style?: ViewStyle;
}

export const Container: React.FC<ContainerProps> = ({
  children,
  center = false,
  padding = false,
  style = {},
  ...props
}) => {
  const theme = useTheme();
  
  return (
    <View
      style={[
        styles.container,
        center && styles.containerCenter,
        padding && { padding: theme.spacing.m },
        style,
      ]}
      {...props}
    >
      {children}
    </View>
  );
};

// ========= KeyboardAwareContainer Component =========
interface KeyboardAwareContainerProps extends KeyboardAvoidingViewProps {
  scrollable?: boolean;
  center?: boolean;
  padding?: boolean;
  style?: ViewStyle;
  contentContainerStyle?: ViewStyle;
  behavior?: 'height' | 'position' | 'padding';
}

export const KeyboardAwareContainer: React.FC<KeyboardAwareContainerProps> = ({
  children,
  scrollable = true,
  center = false,
  padding = false,
  style = {},
  contentContainerStyle = {},
  behavior = Platform.OS === 'ios' ? 'padding' : 'height',
  ...props
}) => {
  const theme = useTheme();

  const containerStyle = [
    styles.container,
    center && styles.containerCenter,
    padding && { padding: theme.spacing.m },
    style,
  ];

  const contentStyle = [
    styles.contentContainer,
    center && styles.containerCenter,
    contentContainerStyle,
  ];

  return (
    <KeyboardAvoidingView
      style={containerStyle}
      behavior={behavior}
      {...props}
    >
      {scrollable ? (
        <ScrollView
          contentContainerStyle={contentStyle}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {children}
        </ScrollView>
      ) : (
        <View style={contentStyle}>{children}</View>
      )}
    </KeyboardAvoidingView>
  );
};

// ========= ResponsiveText Component =========
interface ResponsiveTextProps extends TextProps {
  variant?: 'h1' | 'h2' | 'h3' | 'h4' | 'body' | 'small' | 'tiny';
  color?: string;
  center?: boolean;
  bold?: boolean;
  style?: TextStyle;
}

export const ResponsiveText: React.FC<ResponsiveTextProps> = ({
  children,
  variant = 'body',
  color,
  center = false,
  bold = false,
  style = {},
  ...props
}) => {
  const theme = useTheme();

  return (
    <Text
      style={[
        { fontSize: theme.typography[variant] },
        color ? { color } : {},
        center && styles.textCenter,
        bold && styles.textBold,
        style,
      ]}
      {...props}
    >
      {children}
    </Text>
  );
};

// ========= ResponsiveInput Component =========
interface ResponsiveInputProps extends TextInputProps {
  label?: string;
  error?: string;
  containerStyle?: ViewStyle;
  inputStyle?: TextStyle;
}

export const ResponsiveInput: React.FC<ResponsiveInputProps> = ({
  label,
  error,
  containerStyle = {},
  inputStyle = {},
  ...props
}) => {
  const theme = useTheme();
  
  return (
    <View style={[styles.inputContainer, containerStyle]}>
      {label && (
        <ResponsiveText variant="small" style={styles.inputLabel}>
          {label}
        </ResponsiveText>
      )}
      <TextInput
        style={[
          styles.input,
          { borderColor: error ? theme.colors.danger : theme.colors.lightGray },
          inputStyle,
        ]}
        placeholderTextColor={theme.colors.gray}
        {...props}
      />
      {error && (
        <ResponsiveText variant="small" color={theme.colors.danger} style={styles.errorText}>
          {error}
        </ResponsiveText>
      )}
    </View>
  );
};

// ========= ResponsiveButton Component =========
interface ResponsiveButtonProps extends TouchableOpacityProps {
  title: string;
  variant?: 'primary' | 'secondary' | 'outline' | 'text';
  size?: 'small' | 'medium' | 'large';
  fullWidth?: boolean;
  loading?: boolean;
  disabled?: boolean;
  style?: ViewStyle;
  textStyle?: TextStyle;
}

export const ResponsiveButton: React.FC<ResponsiveButtonProps> = ({
  title,
  variant = 'primary',
  size = 'medium',
  fullWidth = false,
  loading = false,
  disabled = false,
  style = {},
  textStyle = {},
  ...props
}) => {
  const theme = useTheme();
  
  // Determine background color based on variant
  const getBackgroundColor = () => {
    if (disabled) return theme.colors.lightGray;
    
    switch (variant) {
      case 'primary':
        return theme.colors.primary;
      case 'secondary':
        return theme.colors.secondary;
      case 'outline':
      case 'text':
        return theme.colors.transparent;
      default:
        return theme.colors.primary;
    }
  };
  
  // Determine text color based on variant
  const getTextColor = () => {
    if (disabled) return theme.colors.gray;
    
    switch (variant) {
      case 'primary':
        return theme.colors.dark;
      case 'secondary':
        return theme.colors.white;
      case 'outline':
        return theme.colors.primary;
      case 'text':
        return theme.colors.primary;
      default:
        return theme.colors.white;
    }
  };
  
  // Determine button size
  const getButtonSize = () => {
    switch (size) {
      case 'small':
        return {
          paddingVertical: theme.spacing.xs,
          paddingHorizontal: theme.spacing.m,
        };
      case 'large':
        return {
          paddingVertical: theme.spacing.m,
          paddingHorizontal: theme.spacing.xl,
        };
      default: // medium
        return {
          paddingVertical: theme.spacing.s,
          paddingHorizontal: theme.spacing.l,
        };
    }
  };
  
  // Determine border style for outline variant
  const getBorderStyle = () => {
    return variant === 'outline'
      ? { borderWidth: 1, borderColor: theme.colors.primary }
      : {};
  };
  
  return (
    <TouchableOpacity
      style={[
        styles.button,
        { backgroundColor: getBackgroundColor() },
        getBorderStyle(),
        getButtonSize(),
        fullWidth && styles.buttonFullWidth,
        disabled && styles.buttonDisabled,
        style,
      ]}
      disabled={disabled || loading}
      activeOpacity={0.8}
      {...props}
    >
      <ResponsiveText
        color={getTextColor()}
        variant={size === 'small' ? 'small' : 'body'}
        center
        bold
        style={textStyle}
      >
        {title}
      </ResponsiveText>
    </TouchableOpacity>
  );
};

// ========= BackgroundImage Component =========
interface BackgroundImageProps extends ImageBackgroundProps {
  overlay?: boolean;
  overlayColor?: string;
  overlayOpacity?: number;
}

export const BackgroundImage: React.FC<BackgroundImageProps> = ({
  children,
  overlay = false,
  overlayColor = '#000',
  overlayOpacity = 0.5,
  style = {},
  ...props
}) => {
  return (
    <ImageBackground
      style={[styles.backgroundImage, style]}
      {...props}
    >
      {overlay && (
        <View
          style={[
            styles.overlay,
            { backgroundColor: overlayColor, opacity: overlayOpacity },
          ]}
        />
      )}
      {children}
    </ImageBackground>
  );
};

// ========= Card Component =========
interface CardProps extends ViewProps {
  elevated?: boolean;
  elevation?: 'small' | 'medium' | 'large';
  style?: ViewStyle;
}

export const Card: React.FC<CardProps> = ({
  children,
  elevated = true,
  elevation = 'small',
  style = {},
  ...props
}) => {
  const theme = useTheme();
  
  return (
    <View
      style={[
        styles.card,
        { padding: theme.spacing.m, borderRadius: theme.borderRadius.medium },
        elevated && theme.shadows[elevation],
        style,
      ]}
      {...props}
    >
      {children}
    </View>
  );
};

// ========= Row Component =========
interface RowProps extends ViewProps {
  justify?: 'flex-start' | 'flex-end' | 'center' | 'space-between' | 'space-around' | 'space-evenly';
  align?: 'flex-start' | 'flex-end' | 'center' | 'stretch' | 'baseline';
  spacing?: number;
  wrap?: boolean;
  style?: ViewStyle;
}

export const Row: React.FC<RowProps> = ({
  children,
  justify = 'flex-start',
  align = 'center',
  spacing = 0,
  wrap = false,
  style = {},
  ...props
}) => {
  return (
    <View
      style={[
        styles.row,
        {
          justifyContent: justify,
          alignItems: align,
          flexWrap: wrap ? 'wrap' : 'nowrap',
          gap: normalize(spacing),
        },
        style,
      ]}
      {...props}
    >
      {children}
    </View>
  );
};

// ========= Column Component =========
interface ColumnProps extends ViewProps {
  justify?: 'flex-start' | 'flex-end' | 'center' | 'space-between' | 'space-around' | 'space-evenly';
  align?: 'flex-start' | 'flex-end' | 'center' | 'stretch' | 'baseline';
  spacing?: number;
  style?: ViewStyle;
}

export const Column: React.FC<ColumnProps> = ({
  children,
  justify = 'flex-start',
  align = 'stretch',
  spacing = 0,
  style = {},
  ...props
}) => {
  return (
    <View
      style={[
        styles.column,
        {
          justifyContent: justify,
          alignItems: align,
          gap: normalize(spacing),
        },
        style,
      ]}
      {...props}
    >
      {children}
    </View>
  );
};

// ========= Spacer Component =========
interface SpacerProps {
  size?: number | 'xs' | 's' | 'm' | 'l' | 'xl' | 'xxl';
  horizontal?: boolean;
}

export const Spacer: React.FC<SpacerProps> = ({
  size = 'm',
  horizontal = false,
}) => {
  const theme = useTheme();
  
  const getSize = () => {
    if (typeof size === 'number') return normalize(size);
    return theme.spacing[size];
  };
  
  return (
    <View
      style={
        horizontal
          ? { width: getSize() }
          : { height: getSize() }
      }
    />
  );
};

// ========= Styles =========
const styles = StyleSheet.create({
  container: {
    flex: 1,
    width: '100%',
  },
  containerCenter: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  contentContainer: {
    flexGrow: 1,
  },
  textCenter: {
    textAlign: 'center',
  },
  textBold: {
    fontWeight: 'bold',
  },
  inputContainer: {
    marginBottom: verticalScale(16),
    width: '100%',
  },
  inputLabel: {
    marginBottom: verticalScale(4),
    fontWeight: '500',
  },
  input: {
    borderWidth: 1,
    borderRadius: normalize(8),
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(10),
    fontSize: normalizeFont(14),
    width: '100%',
  },
  errorText: {
    marginTop: verticalScale(4),
  },
  button: {
    borderRadius: normalize(8),
    alignItems: 'center',
    justifyContent: 'center',
  },
  buttonFullWidth: {
    width: '100%',
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  backgroundImage: {
    flex: 1,
    width: '100%',
    height: '100%',
  },
  overlay: {
    ...StyleSheet.absoluteFillObject,
  },
  card: {
    backgroundColor: '#fff',
    marginVertical: verticalScale(8),
  },
  row: {
    flexDirection: 'row',
    width: '100%',
  },
  column: {
    flexDirection: 'column',
    width: '100%',
  },
});