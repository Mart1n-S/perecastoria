import React from 'react';
import { Box, Typography, IconButton } from '@mui/material';
import { PlayArrow } from '@mui/icons-material';

const Result = ({ storyData, audioUrl, imageBase64 }) => {
  const base64Audio = audioUrl.startsWith("data:") ? audioUrl : `data:audio/mp3;base64,${audioUrl}`;

  return (
    <Box
      sx={{
        width: '100%',
        maxWidth: '600px',
        bgcolor: 'rgba(255, 255, 255, 0.1)',
        backdropFilter: 'blur(10px)',
        p: 4,
        borderRadius: 3,
        boxShadow: 3,
        textAlign: 'center',
      }}
    >
      <Typography variant="h5" color="white" paragraph>
        {storyData}
      </Typography>

      <Box sx={{ mb: 3 }}>
        <img
          src={`data:image/jpeg;base64,${imageBase64}`}
          alt="Generated"
          style={{ width: '100%', borderRadius: 8 }}
        />
      </Box>

      {audioUrl && (
        <Box>
          <audio controls>
            <source src={base64Audio} type="audio/mp3" />
            Your browser does not support the audio element.
          </audio>
        </Box>
      )}
    </Box>
  );
};

export default Result;
